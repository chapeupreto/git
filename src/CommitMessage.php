<?php
/**
 * This file is part of SebastianFeldmann\Git.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\Git;

/**
 * Class CommitMessage
 *
 * @package SebastianFeldmann\Git
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/git
 * @since   Class available since Release 0.9.0
 */
class CommitMessage
{
    /**
     * Commit Message content
     *
     * This includes lines that are comments.
     *
     * @var string
     */
    private $rawContent;

    /**
     * Content split lines
     *
     * This includes lines that are comments.
     *
     * @var string[]
     */
    private $rawLines;

    /**
     * Amount of lines
     *
     * This includes lines that are comments
     *
     * @var int
     */
    private $rawLineCount;

    /**
     * The comment character
     *
     * Null would indicate the comment character is not set for this commit. A comment character might be null if this
     * commit came from a message stored in the repository, it would be populated if it came from a commit-msg hook,
     * where the commit message can still contain comments.
     *
     * @var string|null
     */
    private $commentCharacter;

    /**
     * Get the lines
     *
     * This excludes the lines which are comments.
     *
     * @var string[]
     */
    private $contentLines;

    /**
     * Get the number of lines
     *
     * This excludes lines which are comments.
     *
     * @var int
     */
    private $contentLineCount;

    /**
     * Commit Message content
     *
     * This excludes lines that are comments.
     *
     * @var string
     */
    private $content;

    /**
     * CommitMessage constructor
     *
     * @param string      $content
     * @param string|null $commentCharacter
     */
    public function __construct(string $content, string $commentCharacter = null)
    {
        $this->rawContent       = $content;
        $this->rawLines         = empty($content) ? [] : preg_split("/\\r\\n|\\r|\\n/", $content);
        $this->rawLineCount     = count($this->rawLines);
        $this->commentCharacter = $commentCharacter;
        $this->contentLines     = $this->getContentLines($this->rawLines, $commentCharacter);
        $this->contentLineCount = count($this->contentLines);
        $this->content          = implode(PHP_EOL, $this->contentLines);
    }

    /**
     * Is message empty.
     *
     * @return bool
     */
    public function isEmpty() : bool
    {
        return empty($this->content);
    }


    /**
     * Get commit message content
     *
     * This excludes lines that are comments.
     *
     * @return string
     */
    public function getContent() : string
    {
        return $this->content;
    }

    /**
     * Get complete commit message content
     *
     * This includes lines that are comments.
     *
     * @return string
     */
    public function getRawContent() : string
    {
        return $this->rawContent;
    }

    /**
     * Return all lines
     *
     * This includes lines that are comments.
     *
     * @return array
     */
    public function getLines() : array
    {
        return $this->rawLines;
    }

    /**
     * Return line count
     *
     * This includes lines that are comments.
     *
     * @return int
     */
    public function getLineCount() : int
    {
        return $this->rawLineCount;
    }

    /**
     * Get a specific line
     *
     * @param  int $index
     * @return string
     */
    public function getLine(int $index) : string
    {
        return isset($this->rawLines[$index]) ? $this->rawLines[$index] : '';
    }

    /**
     * Return first line
     *
     * @return string
     */
    public function getSubject() : string
    {
        return $this->contentLines[0];
    }

    /**
     * Return content from line nr. 3 to the last line
     *
     * @return string
     */
    public function getBody() : string
    {
        return implode(PHP_EOL, $this->getBodyLines());
    }

    /**
     * Return lines from line nr. 3 to the last line
     *
     * @return array
     */
    public function getBodyLines() : array
    {
        return $this->contentLineCount < 3 ? [] : array_slice($this->contentLines, 2);
    }

    /**
     * Get the comment character
     *
     * Null would indicate the comment character is not set for this commit. A comment character might be null if this
     * commit came from a message stored in the repository, it would be populated if it came from a commit-msg hook.
     *
     * @return null|string
     */
    public function getCommentCharacter()
    {
        return $this->commentCharacter;
    }

    /**
     * Get the lines that are not comments
     *
     * Null comment character indicates no comment character.
     *
     * @param  array       $rawLines
     * @param  string|null $commentCharacter
     * @return string[]
     */
    private function getContentLines(array $rawLines, string $commentCharacter = null) : array
    {
        $lines = [];

        foreach($rawLines as $line) {
            if(!isset($line{0}) || $line{0} !== $commentCharacter) {
                $lines[] = $line;
            }
        }

        return $lines;
    }

    /**
     * Create CommitMessage from file
     *
     * @param  string $path
     * @param  string|null $commentCharacter
     * @return \SebastianFeldmann\Git\CommitMessage
     */
    public static function createFromFile(string $path, $commentCharacter = '#') : CommitMessage
    {
        if (!file_exists($path)) {
            throw new \RuntimeException('Commit message file not found');
        }

        return new CommitMessage(file_get_contents($path), $commentCharacter);
    }
}
