<?php
namespace JildertMiedema\WindowsPhone;

class WindowsPhonePushNotification
{
    /**
     *
     * Toast notifications are system-wide notifications that do not disrupt the user
     * workflow or require intervention to resolve. They are displayed at the top
     * of the screen for ten seconds before disappearing. If the toast notification
     * is tapped,the application that sent the toast notification will launch. A
     * toast notification can be dismissed with a flick.
     * Two text elements of a toast notification can be updated:
     *
     * Title. A bolded string that displays immediately after the application icon.
     * Sub-title. A non-bolded string that displays immediately after the Title.
     *
     * @param $uri
     * @param $title
     * @param $subtitle
     * @param int $delay
     * @param null $messageId
     *
     * @return array
     */
    public function pushToast($uri, $title, $subtitle, $delay = WindowsPhonePushDelay::Immediate, $messageId = null)
    {
        $msg =  "<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
                "<wp:Notification xmlns:wp=\"WPNotification\">" .
                "<wp:Toast>" .
                "<wp:Text1>".htmlspecialchars($title)."</wp:Text1>" .
                "<wp:Text2>".htmlspecialchars($subtitle)."</wp:Text2>" .
                "</wp:Toast>" .
                "</wp:Notification>";

        return $this->push($uri, 'toast', $delay + 2, $messageId, $msg);
    }


    /**
     * @param $uri
     * @param $target type of notification
     * @param $delay immediate, in 450sec or in 900sec
     * @param $messageId The optional custom header X-MessageID uniquely identifies a notification message. If it is present, the same value is returned in the notification response. It must be a string that contains a UUID
     * @param $message The message to send
     *
     * @throws PushException
     * @return array
     */
    private function push($uri, $target, $delay, $messageId, $message)
    {
        $headers =  array(
            'Content-Type: text/xml',
            'Accept: application/*',
            "X-NotificationClass: $delay"
        );

        if($messageId != NULL) {
            $headers[] = "X-MessageID: $messageId";
        }
        if($target != NULL) {
            $headers[] = "X-WindowsPhone-Target:$target";
        }

        $request = curl_init();
        curl_setopt($request, CURLOPT_HEADER, true);
        curl_setopt($request, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($request, CURLOPT_POST, true);
        curl_setopt($request, CURLOPT_POSTFIELDS, $message);
        curl_setopt($request, CURLOPT_URL, $uri);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($request);

        if($errorNumber = curl_errno($request)) {
            $errorMessage = curl_strerror($errorNumber);
            throw new PushException($errorMessage, $errorNumber);
        }

        curl_close($request);

        $result = array();

        foreach (explode("\n",$response) as $line) {
            $tab = explode(":", $line, 2);
            if(count($tab) == 2) {
                $result[$tab[0]] = trim($tab[1]);
            }
        }

        return $result;
     }
}
