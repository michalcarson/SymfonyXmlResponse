<?php
namespace SymfonyXmlResponse\Responses;

class XmlRepeater implements XmlDecoratorInterface
{
    protected $placeholder;
    protected $repeatingElement;
    protected $repeatingData;

    /**
     * XmlRepeater constructor.
     * @param string $placeholder   marker to look for in the content and replace with new (repeating) content
     * @param string $repeatingElement  name of the XML element that repeats
     * @param array $repeatingData  indexed array of repeating data
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

        return str_replace($this->placeholder, implode("\n", $xml), $content);
    }
}
