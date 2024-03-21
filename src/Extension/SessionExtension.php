<?php
namespace MyApp\Extension;
class SessionExtension extends \Twig\Extension\AbstractExtension {
public function getFunctions() {
return [
new \Twig\TwigFunction('clear_session_message', [$this, 'clearSessionMessage']),
]; }
public function clearSessionMessage() {
$_SESSION['message'] = null; }
}