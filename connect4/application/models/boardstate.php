<?php
class BoardState {
    public function serialize() {
        return serialize($this->state);
    }
    public function unserialize($s) {
        $this->state = unserialize($s);
    }

    public $state = array(
                          array(0,0,0,0,0,0,0),
                          array(0,0,0,0,0,0,0),
                          array(0,0,0,0,0,0,0),
                          array(0,0,0,0,0,0,0),
                          array(0,0,0,0,0,0,0),
                          array(0,0,0,0,0,0,0));
}
