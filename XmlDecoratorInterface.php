<?php
namespace SymfonyXmlResponse\Responses;

interface XmlDecoratorInterface
{
    /**
     * Accepts the current content, makes prescribed modifications and
     * returns the modified content.
     *
     * @param string $content
     * @return string
     */
    public function run($content);

    /**
     * Returns a boolean value indicating whether the decorator succeeded.
     *
     * @return boolean
     */
    public function isSuccess();
}
