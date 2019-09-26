<?php

namespace Bone\View\Helper;

class AlertBox
{
    /**
     * @param array $message array of messages, last item in array should be alert class
     * @return bool|string
     */
    public function alertBox(array $message)
    {
        $class = $this->getClass($message);

        $alert = '<div class="alert '.$class.'"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'.$this->renderMessage($message).'</div>';
        return $alert;
    }

    /**
     * @param array $message
     * @return string
     */
    private function getClass(array $message)
    {
        if(count($message) < 2) {
            return 'alert-info';
        }
        $class = array_pop($message);
        $class = (!strstr($class, 'alert-')) ? 'alert-'.$class : '';
        return $class;
    }

    /**
     * @param array $message
     * @return string
     */
    private function renderMessage(array $message)
    {
        if (isset($message[1])) {
            unset($message[1]);
        }
        $alert = '';
        $num = count($message);
        $x = 1;
        foreach($message as $msg)
        {
            $alert .= $msg;
            if($x < $num){$alert .= '<br />';}
            $x ++;
        }
        return $alert;
    }
}