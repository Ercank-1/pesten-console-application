<?php

enum GameResultCase {
    case Winner;
    case TopCardTypeChanged;
    case Played;
    case TakesCard;
    case NotPLAYED;
    case NobodyCanPlay;
}