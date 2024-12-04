<?php
namespace Controllers;

interface BaseController
{
    /**
     * @param array $get
     */
    public function handleGETRequest(array $get = []): void;

    /**
     * @param array $post
     */
    public function handlePOSTRequest(array $post = []): void;
}
?>
