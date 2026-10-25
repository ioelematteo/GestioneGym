<?php

namespace App\Exceptions;

use RuntimeException;

/** L'IA non ha risposto (chiave sbagliata, rete, servizio giù...). */
class AiUnavailableException extends RuntimeException {}
