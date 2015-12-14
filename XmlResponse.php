<?php

namespace SymfonyXmlResponse\Responses;

/**
 * Simple XML response writer in the Symfony Response model.
 *
 * For the origin of much of this code, @see http://php.net/manual/en/ref.xmlwriter.php. Proper credit should go
 * to massimo71, Alexandre Arica and others.
 *
 * @author Michal Carson <michal.carson@carsonsoftwareengineering.com>
 */
use Symfony\Component\HttpFoundation\Response;

class XmlResponse extends Response
{

    protected $data = '';

    /**
     * instance of the built-in PHP XMLWriter.
     * @var \XMLWriter
     */
    protected $xml_writer;

    /**
     * Name for the root element of the XML document.
     * @var string
     */
    public $root_element_name = 'document';

    /**
     * File path for XSL Transform file. Will be included in XML header if set.
     * @var string
     */
    public $xslt_file_path = '';

    /**
     * Array of objects that implement the XmlDecoratorInterface, each of which
     * will be run as the content is rendered.
     * @var array
     */
    protected $decorators = array();

    /**
     * Constructor.
     *
     * @param mixed $data    The response data
     * @param int   $status  The response status code
     * @param array $headers An array of response headers
     */
    public function __construct($data = null, $status = 200, $headers = array())
    {
        parent::__construct('', $status, $headers);

        if (null === $data) {
            $data = new \ArrayObject();
        }

        $this->xml_writer = new \XMLWriter();

        if (!is_null($data)) {
            $this->setData($data);
        }

    }

    /**
     * {@inheritdoc}
     */
    public static function create($data = null, $status = 200, $headers = array())
    {
        return new static($data, $status, $headers);

    }

    /**
     * Sets the data to be sent as XML.
     *
     * @param mixed $data
     *
     * @return XmlResponse
     *
     * @throws \InvalidArgumentException
     */
    public function setData($data = array())
    {
        try {
            $this->startDocument($this->root_element_name, $this->xslt_file_path);
            $this->fromArray($data, $this->root_element_name);
            $this->data = $this->getDocument();

        } catch (\Exception $exception) {
            throw $exception;
        }

        return $this->update();

    }

    /**
     * Updates the content and headers
     *
     * @return XmlResponse
     */
    protected function update()
    {
        // Only set the header when there is none
        // in order to not overwrite a custom definition.
        if (!$this->headers->has('Content-Type')) {
            $this->headers->set('Content-Type', 'application/xml');
        }

        return $this->setContent($this->data);

    }

    /**
     * Constructor.
     * @author Alexandre Arica
     * @param string $prm_rootElementName A root element's name of a current xml document
     * @param string $prm_xsltFilePath Path of a XSLT file.
     * @access protected
     * @param null
     */
    protected function startDocument($prm_rootElementName, $prm_xsltFilePath = '')
    {
        $this->xml_writer->openMemory();
        $this->xml_writer->setIndent(true);
        $this->xml_writer->setIndentString(' ');
        $this->xml_writer->startDocument('1.0', 'UTF-8');

        if ($prm_xsltFilePath) {
            $this->xml_writer->writePi('xml-stylesheet', 'type="text/xsl" href="' . $prm_xsltFilePath . '"');
        }

        $this->xml_writer->startElement($prm_rootElementName);

    }

    /**
     * Set an element with a text to a current xml document.
     * @author Alexandre Arica
     * @access protected
     * @param string $prm_elementName An element's name
     * @param string $prm_ElementText An element's text
     * @throws \InvalidArgumentException
     * @return null
     */
    protected function setElement($prm_elementName, $prm_ElementText)
    {
        if (!isset($prm_elementName)) {
            throw new \InvalidArgumentException('Element name cannot be null. ' . var_export($prm_elementName, true));
        }
        if (preg_match('/[a-zA-Z]/', substr($prm_elementName, 0, 1)) !== 1) {
            throw new \InvalidArgumentException(
                'Element name must begin with alpha character. ' . var_export($prm_elementName, true)
            );
        }
        $this->xml_writer->startElement($prm_elementName);
        $this->xml_writer->text($prm_ElementText);
        $this->xml_writer->endElement();

    }

    /**
     * Construct elements and texts from an array.
     * The array should contain an attribute's name in index part
     * and a attribute's text in value part.
     * @author Alexandre Arica
     * @author massimo71
     * @access protected
     * @param array $prm_array Contains attributes and texts
     * @param string $prm_name Name of the element described by this array
     * @return null
     */
    protected function fromArray($prm_array, $prm_name)
    {
        if (is_array($prm_array)) {
            foreach ($prm_array as $index => $element) {
                if (is_array($element)) {
                    $this->xml_writer->startElement($index);
                    $this->fromArray($element, $index);
                    $this->xml_writer->endElement();

                } elseif (substr($index, 0, 1) == '@') {
                    $this->xml_writer->writeAttribute(substr($index, 1), $element);

                } elseif ($index == $prm_name) {
                    $this->xml_writer->text($element);

                } else {
                    $this->setElement($index, $element);
                }
            }

        }

    }

    /**
     * Return the content of a current xml document.
     * @author Alexandre Arica
     * @access protected
     * @param null
     * @return string Xml document
     */
    protected function getDocument()
    {
        $this->xml_writer->endElement();
        $this->xml_writer->endDocument();
        return $this->xml_writer->outputMemory();

    }

    /**
     * Return an XML fragment (rather than a fully formated XML file).
     *
     * @param string $name      outermost element name
     * @param mixed $content    array or string content for the element
     * @return string
     */
    public function getFragment($name, $content)
    {
        $this->xml_writer->openMemory();
        $this->xml_writer->setIndent(true);
        $this->xml_writer->setIndentString(' ');

        if (is_array($content)) {

            $this->xml_writer->startElement($name);
            $this->fromArray($content, $name);
            $this->xml_writer->endElement();

        } else {

            $this->setElement($name, $content);

        }

        return $this->xml_writer->outputMemory();
    }

    /**
     * Sends content for the current web response.
     * Process all decorators before sending the content.
     *
     * @return Response
     */
    public function sendContent()
    {
        foreach ($this->decorators as $decor) {
            $this->content = $decor->run($this->content);
        }

        return parent::sendContent();
    }

    /**
     * Add a decorator to the stack.
     *
     * @param XmlDecoratorInterface $decorator
     * @return XmlResponse
     */
    public function addDecorator(XmlDecoratorInterface $decorator)
    {
        $this->decorators[] = $decorator;

        return $this;
    }

    /**
     * Retrieve the decorator stack for inspection.
     *
     * @return array
     */
    public function getDecorators()
    {
        return $this->decorators;
    }
}
