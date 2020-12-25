<?php


namespace App\Model\Front\UI\Elements;


use App\Model\Front\UI\IElement;
use Nette\SmartObject;

/**
 * Class Card
 * @package App\Model\Front\UI
 */
class Card implements IElement
{
    use SmartObject;

    private string $title;
    private Text $text;

    /**
     * Card constructor.
     * @param string $title
     * @param Text $text
     */
    public function __construct(string $title, Text $text)
    {
        $this->title = $title;
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return Text
     */
    public function getText(): Text
    {
        return $this->text;
    }

    /**
     * @inheritDoc
     * @return string
     */
    public function toJSON(): string
    {
        return json_encode(get_object_vars($this)) ?: "{}";
    }

    /**
     * @inheritDoc
     * @return string
     */
    public function __toString(): string
    {
       return $this->toJSON();
    }

    /**
     * @inheritDoc
     * @return Card
     */
    public static function example(): Card
    {
        return new self("Název karty", Text::example());
    }
}