<?php

namespace Schachbulle\ContaoInternetschachBundle\Classes\PGN;

class PgnParser
{
	/**
	 * Absolute path, e.g. "/path/to/games.pgn".
	 * @var string
	 */
	private $filePath;

	/**
	 * If "filePath" is "/path/to/games.pgn", this is "games.pgn".
	 * @var string
	 */
	private $fileName;

	/**
	 * @var Game[]
	 */
	private $games;

	private $multiLineAnnotationDepth = 0;

	/**
	 * @var Game[]
	 */
	private $currentGame;

	/**
	 * @param string $filePath The absolute or relative path to a file. Must not be a directory.
	 */
	public function __construct($filePath)
	{
		$this->filePath = $filePath;
		$this->fileName = basename($filePath);
		$this->parse();
	}

	/**
	 * @return Game[] All of the games from the file.
	 */
	public function getGames()
	{
		return $this->games;
	}

	/**
	 * @return Game
	 */
	public function getGame($index)
	{
		return $this->games[$index];
	}

	/**
	 * @return int How many games were in the file.
	 */
	public function countGames()
	{
		return count($this->games);
	}

	private function createCurrentGame()
	{
		$this->currentGame = new Game();
		$this->currentGame->setFromPgnDatabase($this->fileName);
		$this->multiLineAnnotationDepth = 0;
	}

	/**
	 * Funktion parse
	 * ======================================================================================
	 * Extrahiert aus einer PGN-Datei die einzelnen Partien und speichert diese im Array $this->games
	 */
	private function parse()
	{
		$handle = fopen($this->filePath, "r");

		$this->createCurrentGame(); // Aktuelles Game-Objekt $this->currentGame anlegen
		$pgnBuffer = null;
		$haveMoves = false;

		while(($line = fgets($handle, 4096)) !== false)
		{
			$line = trim($line); // Leerzeichen/Umbrüche in Zeile vorn und hinten entfernen
			if(empty($line))
			{
				continue; // Zeile ist leer, mit nächster Zeile fortsetzen
			}

			if(strpos($line, '[') === 0 && $this->multiLineAnnotationDepth === 0)
			{
				// Beginnt die Zeile mit [ sind es Metadaten.
				// Wenn Metadaten UND Züge vorhanden sind, ist die Partie komplett
				if($haveMoves)
				{
					// Nächste Partie
					$this->completeCurrentGame($pgnBuffer);
					$this->createCurrentGame();
					$haveMoves = false;
					$pgnBuffer = null;
				}

				$this->addMetaData($line);
				$pgnBuffer .= $line . "\n";
			}
			else
			{
				// This is a line of moves.
				$this->addMoves($line);
				$haveMoves = true;
				$pgnBuffer .= "\n" . $line;
			}
		}

		$this->completeCurrentGame($pgnBuffer);

		echo "<pre>";
		print_r($this->getGames());
		echo "</pre>";
		fclose($handle);
	}

	private function removeAnnotations($line)
	{
		$result = null;
		foreach(str_split($line) as $char)
		{
			if($char === '{' || $char === '(')
			{
				$this->multiLineAnnotationDepth++;
			}
			if($this->multiLineAnnotationDepth === 0)
			{
				$result .= $char;
			}
			if ($char === '}' || $char === ')')
			{
				$this->multiLineAnnotationDepth--;
			}
		}

		return $result;
	}

	/**
	 * @param string $line "[Date "1953.??.??"]"
	 */
	private function addMetaData($line)
	{
		if(strpos($line, ' ') === false)
		{
			throw new \Exception("Invalid metadata: " . $line);
		}

		list($key, $val) = explode(' ', $line, 2);
		$key = strtolower(trim($key, '['));
		$val = trim($val, '"]');

		switch($key)
		{
			case 'event':
				$this->currentGame->setEvent($val);
				break;
			case 'site':
				$this->currentGame->setSite($val);
				break;
			case 'date':
			case 'eventdate':
				if(!$this->currentGame->getDate())
				{
					$this->currentGame->setDate($val);
				}
				break;
			case 'round':
				$this->currentGame->setRound($val);
				break;
			case 'white':
				$this->currentGame->setWhite($val);
				break;
			case 'black':
				$this->currentGame->setBlack($val);
				break;
			case 'whiteelo':
				$this->currentGame->setWhiteElo($val);
				break;
			case 'blackelo':
				$this->currentGame->setBlackElo($val);
				break;
			case 'result':
				$this->currentGame->setResult($val);
				break;
			case 'eco':
				$this->currentGame->setEco($val);
				break;
			case 'plycount':
				$this->currentGame->setMovesCount($val);
				break;
			default:
				// Ignore others
				break;
		}
	}

	/**
	 * @param string $line "Qe7 22. Nhg4 Nxg4 23. Nxg4 Na5 24. b3 Nc6"
	 */
	private function addMoves($line)
	{
		$line = $this->removeAnnotations($line);

		// Remove the move numbers, so "1. e4 e5 2. f4" becomes "e4 e5 f4"
		$line = preg_replace('/\d+\./', '', $line);

		// Remove the result (1-0, 1/2-1/2, 0-1) from the end of the line, if there is one.
		$line = preg_replace('/(1-0|0-1|1\/2-1\/2|\*)$/', '', $line);

		// If black's move is after an annotation, it is formatted as: "annotation } 17...h5".
		// Remove those dots (one is already gonee after removing "17." earlier.
		$line = str_replace('..', '', $line);

		$line = preg_replace('/\$[0-9]+/', '', $line);
		$line = preg_replace('/\([^\(\)]+\)/', '', $line);

		// And finally remove excess white-space.
		$line = trim(preg_replace('/\s{2,}/', ' ', $line));

		$this->currentGame->setMoves($this->currentGame->getMoves() ? $this->currentGame->getMoves() . " " . $line : $line);
	}

	private function completeCurrentGame($pgn)
	{
		$this->currentGame->setPgn($pgn);
		$this->games[] = $this->currentGame;
		$this->multiLineAnnotationDepth = 0;
	}
}
