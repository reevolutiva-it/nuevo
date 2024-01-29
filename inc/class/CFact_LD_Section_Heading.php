<?php

/**
 * Path: wp-content/plugins/coursefac-integration/inc/class/CFact_LD_Section_Heading.php
 * Este fichero contiene un clase que representa un encabezado de seccion de un curso de LearnDash. */

class CFact_LD_Section_Heading {
    public $order;
    public $post_title;
    public $ID;
    public $expanded;
    public $tree;
    public $type;
    public $url;

    public function __construct($order, $post_title) {
        $this->order = $order;
        $this->post_title = $post_title;
        $this->ID = rand(0, 99999);
        $this->expanded = false;
        $this->tree = array();
        $this->type = "section-heading";
        $this->url = "";
    }
}
