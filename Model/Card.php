<?php

class Card
{
    const CARD_TYPES = ['klaveren', 'ruiten', 'schoppen', 'harten'];

    private string $value;
    private string $type;
    private string $icon;

    public function __construct(string $name, string $type)
    {
        $this->value = $name;
        $this->type = $type;
        $this->setIcon($type);
    }

    public function setIcon(string $type)
    {
        switch ($type) {
            case 'klaveren':
                $this->icon = '&clubs;'; 
                break;
            case 'ruiten':
                $this->icon = '&diams;';
                break;
            case 'schoppen':
                $this->icon = '&spades;';
                break;
            case 'harten':
                $this->icon = '&hearts;';
                break;  
        }
    }

    public function getIcon()
    {
        return html_entity_decode($this->icon, 0, 'UTF-8');
    }

    public function getValue():string
    {
        return $this->value;
    }

    public function getType()
    {
        return $this->type;
    }

}