<?php

namespace Bone\View\Helper;


class AlertBox
{
    /**
     * @param $message
     * @return string
     */
    public static function alertBox($message): string
    {
        if (!$message) {
            return '';
        }
        
        if (!is_array($message)) {
            $text = $message;
            $message = array(
                'class' => 'warning',
                'message' => $text
            );
        }
        
        $alert = '<div class="alert ';
        if ($message['class'] != 'alert') {
            $alert .= 'alert-' . $message['class'];
        }
        
        $alert .= '"><button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>';
        
        if (!is_array($message['message'])) {
            $alert .= $message['message'];
            
        } else {
            $num = count($message['message']);
            $x = 1;
            foreach ($message['message'] as $message) {
                $alert .= $message;
                if ($x < $num) {
                    $alert .= '<br />';
                }
                $x++;
            }
        }
        
        $alert .= '</div>';
        
        return $alert;
    }
}