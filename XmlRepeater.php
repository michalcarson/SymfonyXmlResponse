<?php
namespace SymfonyXmlResponse\Responses;

class XmlRepeater implements XmlDecoratorInterface
{
    /** @var mixed */
    protected $placeholder;

    /** @var string */
    protected $repeatingElement;

    /** @var array */
    protected $repeatingData;

    /** @var boolean */
    private $success = false;

    /**
     * XmlRepeater constructor.
     *
     * @param string $placeholder      Marker to look for in the content and replace with new (repeating) content.
     * @param string $repeatingElement Name of the XML element that repeats.
     * @param array  $repeatingData    Indexed array of repeating data.
     */
    public function __construct($placeholder, $repeatingElement, array $repeatingData)
    {
        $this->placeholder = $placeholder;
        $this->repeatingElement = $repeatingElement;
        $this->repeatingData = $repeatingData;
    }

    /**
     * Accepts the current content, makes prescribed modifications and
     * returns the modified content.
     *
     * @param string $content
     * @return string
     */
    public function run($content)
    {
        $xml = array();
        $xr = new XmlResponse();

        foreach ($this->repeatingData as $item) {
            $xml[] = $xr->getFragment($this->repeatingElement, $item);
        }

        $replacements = 0;
        $content = str_replace($this->placeholder, implode("\n", $xml), $content, $replacements);

        $this->success = $replacements > 0;

        return $content;
    }

    /**
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }
}
