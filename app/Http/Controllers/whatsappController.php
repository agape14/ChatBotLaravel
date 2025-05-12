<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use phpDocumentor\Reflection\PseudoTypes\LowercaseString;

class whatsappController extends Controller
{

  public $ws_token;
  public $ws_endpoint;
  public $ws_tokenwebhook;

  public function __construct()
  {
      $this->ws_token = env('WHATSAPP_TOKEN');
      $this->ws_endpoint = env('ENDPOINT_RPTA');
      $this->ws_tokenwebhook = env('TOKEN_WEBHOOK');
  }

  function ws_responder_texto($numero, $body)
  {
    return [
      'headers' => [
        'Content-Type' => 'application/json', // Tipo de contenido
        'Authorization' => 'Bearer ' . $this->ws_token,  // Incluye el token de autorización
      ],
      'json' => [
        'messaging_product' => 'whatsapp',
        'to' => $numero,  // Número de destino
        'type' => 'text', // Tipo de mensaje
        'text' => [
          'body' => $body, // Cuerpo, en este caso texto
        ],
      ]
    ];
  }

  function ws_responder_ubicacion($numero, $latitud, $longitud)
  {

    return  [
      'headers' => [
        'Authorization' => 'Bearer ' . $this->ws_token,  // Incluye el token de autorización
        'Content-Type' => 'application/json',  // Tipo de contenido
      ],
      'json' => [
        'messaging_product' => 'whatsapp',
        'to' => $numero,  // Número de destino
        'type' => 'location',  // Tipo de mensaje
        'location' => [
          'latitude' => $latitud,  // Latitud de la ubicación
          'longitude' => $longitud,  // Longitud de la ubicación
          'name' => 'Ubicación de Angel Geraldo Tech',  // Nombre de la ubicación (opcional)
          'address' => 'Dirección de ejemplo',  // Dirección (opcional)
        ],
      ]
    ];
  }

  function ws_responder_pdf($numero, $pdf_url)
  {
      return [
          'headers' => [
              'Authorization' => 'Bearer ' . $this->ws_token, // Token de autorización
              'Content-Type' => 'application/json', // Tipo de contenido
          ],
          'json' => [
              'messaging_product' => 'whatsapp',
              'to' => $numero, // Número de destino
              'type' => 'document', // Tipo de mensaje (documento)
              'document' => [
                  'link' => $pdf_url, // URL del PDF
                  'caption' => 'Aquí tienes el documento solicitado.', // Mensaje opcional
                  'filename' => 'VICIdial_White-Paper_20250130.pdf' // Nombre del archivo opcional
              ],
          ]
      ];
  }

  function ws_responder_audio($numero, $audio_url)
  {
      return [
          'headers' => [
              'Authorization' => 'Bearer ' . $this->ws_token, // Token de autorización
              'Content-Type' => 'application/json', // Tipo de contenido
          ],
          'json' => [
              'messaging_product' => 'whatsapp',
              'to' => $numero, // Número de destino
              'type' => 'audio', // Tipo de mensaje (audio)
              'audio' => [
                  'link' => $audio_url // URL del archivo de audio
              ],
          ]
      ];
  }

  function ws_responder_video_youtube($numero, $youtube_url)
  {
      return [
          'headers' => [
              'Authorization' => 'Bearer ' . $this->ws_token, // Token de autorización
              'Content-Type' => 'application/json', // Tipo de contenido
          ],
          'json' => [
              'messaging_product' => 'whatsapp',
              'to' => $numero, // Número de destino
              'type' => 'text', // Tipo de mensaje (texto con enlace)
              'text' => [
                  'preview_url' => true, // Habilita la previsualización del enlace
                  'body' => $youtube_url // Enlace de YouTube
              ],
          ]
      ];
  }

  // Bono!
  function ws_responder_botones($numero)
  {
      return [
          'headers' => [
              'Authorization' => 'Bearer ' . $this->ws_token, // Token de autorización
              'Content-Type' => 'application/json', // Tipo de contenido
          ],
          'json' => [
              'messaging_product' => 'whatsapp',
              'to' => $numero, // Número de destino
              'type' => 'interactive', // Tipo de mensaje interactivo
              'interactive' => [
                  'type' => 'button', // Tipo de interacción
                  'body' => [
                      'text' => 'Selecciona una opción:' // Texto del mensaje
                  ],
                  'action' => [
                      'buttons' => [
                          [
                              'type' => 'reply',
                              'reply' => [
                                  'id' => 'registro',
                                  'title' => 'Registrarse'
                              ]
                          ],
                          [
                              'type' => 'reply',
                              'reply' => [
                                  'id' => 'ver_balance',
                                  'title' => 'Ver Balance'
                              ]
                          ],
                          [
                              'type' => 'reply',
                              'reply' => [
                                  'id' => 'realizar_pago',
                                  'title' => 'Realizar Pago'
                              ]
                          ]
                      ]
                  ]
              ]
          ]
      ];
  }


    function enviarRespuesta($comentario, $numero, $id, $timestamp, $from)
    {
        $endpoint = $this->ws_endpoint;
        $comentario = Str::lower(trim($comentario));

        $opciones = [
            '1' => <<<TXT
            📌 Tenemos 8 terrenos disponibles en seis distritos de Lima Metropolitana.
            Cada uno cuenta con distintas características y precios base. Puedes revisar el listado completo y detalles en este enlace: 🔗 [https://emilima.com.pe/Subastas/catalogo_subasta_2025.pdf]
            TXT,

            '2' => <<<TXT
            Para participar en la subasta, sigue estos pasos:

            1️⃣ Compra tus bases a S/50.00
            Presencial: Pago en el Banco de Crédito (Cuenta Corriente N° 193-11271150-99 a nombre de EMILIMA S.A.) y presentación del comprobante en la Subgerencia de Tesorería.
            Virtual: A través de la página web www.emilima.com.pe/home.

            2️⃣ Depósito de garantía
            Depósito bancario al N° Cuenta Corriente Soles: 191-4217528-0-91 con N° Código de Cuenta Interbancaria: 00219100421752809158, de EMILIMA - FOMUR, remitido al correo subasta@emilima.com.pe, indicando datos completos y el lote a postular, a fin de verificar y brindarle el recibo.
            Cheque de Gerencia No Negociable a nombre de EMILIMA - FOMUR, por el (los) predio(s) a los que postule, presentándose a la Subgerencia de Tesorería.

            3️⃣ Inscripción
            Presencial: Jr. Cuzco N° 286, Cercado de Lima (mesa de partes).
            Virtual: www.sgd.emilima.com.pe/mesapartesvirtual.html.

            📌 Inscripciones hasta el viernes 23 de mayo. Para más detalles, revisa: https://beacons.ai/emilima.sa
            TXT,

          '3' => <<<TXT
            📋 Requisitos para participar:

            📌 Para personas naturales:
            * Anexo 03 de las Bases ([Descargar PDF] https://emilima.com.pe/Subastas/anexo_03_bases.pdf )
            * Declaración Jurada de procedencia lícita de fondos ([Descargar PDF] https://emilima.com.pe/Subastas/declaracion_procedencia_licita_fondos_2025.pdf )
            * Copia de DNI.
            * Comprobante de compra de bases emitido por EMILIMA S.A.
            * Recibo de caja por concepto de garantía emitido por EMILIMA S.A.

            📌 Para personas jurídicas:
            * Anexo 03 de las Bases ([Descargar PDF] https://emilima.com.pe/Subastas/anexo_03_bases.pdf )
            * Declaración Jurada de procedencia lícita de fondos ([Descargar PDF] https://emilima.com.pe/Subastas/declaracion_procedencia_licita_fondos_2025.pdf )
            * Copia de DNI.
            * Copia de RUC y Vigencia de poder del representante legal.
            * Comprobante de compra de bases emitido por EMILIMA S.A.
            * Recibo de caja por concepto de garantía emitido por EMILIMA S.A.

            📆 Fecha de la subasta: domingo 25 de mayo 2025
            📍 Lugar: Museo Metropolitano de Lima (Sala Taulichusco), Av. 28 de julio con Av. Garcilaso de la Vega – Parque de la Exposición, Cercado de Lima
            ⏰ Hora: 9:00 a.m.
            🔹 Modalidad: Mixta (presencial y virtual para postores fuera de Lima Metropolitana)
            TXT,

            '4' => <<<TXT
            Actualmente, EMILIMA ha puesto a disposición 13 espacios comerciales para arrendamiento público en las siguientes zonas:

            📍 Parque de la Exposición
            Módulos de venta, cafetería y baños
            Áreas desde 6.25 m² hasta 213.42 m²
            Renta base mensual desde S/ 1,336.00 hasta S/ 13,810.00
            Para usos como: venta de alimentos, servicios higiénicos y módulos de kiosco

            📍 Cercado de Lima
            Servicios higiénicos, oficina y locales comerciales
            Áreas desde 20.00 m² hasta 102.59 m²
            Renta base mensual desde S/ 452.60 hasta S/ 9,746.00

            🔗 Puedes ver el listado completo y detallado en el siguiente enlace:
            👉 [https://emilima.com.pe/Subastas/LISTA-DE-ESPACIOS-Y-O-INMUEBLES.png]
            TXT,
            '5' => <<<TXT
            Para participar en la subasta, sigue estos pasos:

            1️⃣ Compra tus bases – S/ 50.00
            🛒 Disponibles del 09 al 23 de mayo de 2025

            Presencial: Pago en el Banco de Crédito (Cuenta Corriente N° 193-11271150-99 o CCI:00219300112711509914 a nombre de EMILIMA S.A.) y presentación del comprobante en la Subgerencia de Tesorería.
            Virtual: A través de la página web www.emilima.com.pe/home.

            📩 Enviar el voucher al correo subasta@emilima.com.pe.
            Una vez validado, recibirás las bases en PDF y el comprobante de pago correspondiente.


            2️⃣ Depósito de garantía
            Deberás entregar un cheque de gerencia no negociable, según el tipo de espacio:

            Para espacios en el Parque de la Exposición:
            Monto: equivalente a 2 meses de renta mensual (ver Anexo 01)
            A nombre de: Municipalidad Metropolitana de Lima (RUC 20131380951)

            Para inmuebles del Cercado de Lima:
            Monto: equivalente a 3 meses de renta mensual
            A nombre de: EMILIMA S.A. (RUC 20126236078)

            📍 Entrega presencial del cheque en:
            Jr. Cuzco N° 286, Cercado de Lima – Subgerencia de Tesorería y Recaudación
            🕐 Horario: 8:30 a.m. a 1:00 p.m. y 2:00 p.m. a 4:30 p.m.
            📅 Hasta el viernes 23 de mayo de 2025

            📌 Tras revisión del cheque, se te entregará el recibo de caja, único documento que te acredita como postor hábil.

            TXT,
            '6' => <<<TXT
            📋 Requisitos para participar:

            📌 Para personas naturales:

            Anexo 03 – Declaración Jurada (Descargar PDF)
            Declaración Jurada de procedencia lícita de fondos (Descargar PDF)
            Copia de DNI
            Comprobante de compra de bases emitido por EMILIMA S.A.
            Recibo de caja por concepto de garantía emitido por EMILIMA S.A.

            📌 Para personas jurídicas:

            Anexo 03 – Declaración Jurada (Descargar PDF)
            Declaración Jurada de procedencia lícita de fondos (Descargar PDF)
            Copia de DNI del representante legal
            Copia de RUC y vigencia de poder (SUNARP – no mayor a 30 días)
            Comprobante de compra de bases emitido por EMILIMA S.A.
            Recibo de caja por concepto de garantía emitido por EMILIMA S.A.

            📆 Fecha del acto de subasta:
            Domingo 25 de mayo de 2025
            📍 Lugar: Museo Metropolitano de Lima – Sala Taulichusco (Av. 28 de julio con Av. Garcilaso de la Vega – Parque de la Exposición, Cercado de Lima)
            ⏰ Hora: 11:30 a.m. (máxima tolerancia: 10 minutos)
            🔹 Modalidad: Presencial

            TXT,
            '7' => <<<TXT
            📍 Oficina: Jr. Cuzco N° 286, Cercado de Lima
            📲 Celulares: 989-346-982 / 987-658-263
            🌐 Web: www.emilima.com.pe/home

            📞 Nuestro equipo está listo para responder todas tus dudas en los celulares mencionados.

            TXT,
        ];

        // Detectar "hola"
        if (Str::contains($comentario, ['hola','Hola','buenos','dias','subasta','informacion','información'])) {
            $respuesta = <<<MENU

            👋 ¡Hola! Soy Emi, el asistente virtual de la Empresa Municipal Inmobiliaria de Lima - EMILIMA.

            Hemos lanzado la convocatoria para nuestras subastas públicas y estoy aquí para brindarte toda la información que necesites. 📢

            SUBASTA DE TERRENOS:
            1️⃣ Ver la lista de inmuebles en subasta 📜🏡
            2️⃣ Cómo participar en la subasta de inmuebles 🏢📈
            3️⃣ Fechas y requisitos para participar en la subasta de inmuebles 📅✅

            SUBASTA DE ARRENDAMIENTO DE ESPACIOS COMERCIALES:
            4️⃣ Ver los espacios comerciales disponibles en arrendamiento 🛍️📌
            5️⃣ Cómo participar en la subasta de arrendamiento 💼📊
            6️⃣ Fechas y requisitos para arrendamiento comercial 🗓️📋

            OTROS:
            7️⃣ Contacto 📞📩

            🔹 Escribe el número de la opción que deseas.
            🔹 Escribe "menú" para ver nuevamente las opciones.
            🔹 Escribe "salir" para cerrar el chat.
            MENU;
        }// Detectar opciones 1 al 4
        elseif (array_key_exists($comentario, $opciones)) {
            $respuesta = $opciones[$comentario];
        }// Detectar menú
        elseif (Str::contains($comentario, ['menu', 'menú'])) {
            $respuesta = <<<MENU
            SUBASTA DE TERRENOS:
            1️⃣ Ver la lista de inmuebles en subasta 📜🏡
            2️⃣ Cómo participar en la subasta de inmuebles 🏢📈
            3️⃣ Fechas y requisitos para participar en la subasta de inmuebles 📅✅

            SUBASTA DE ARRENDAMIENTO DE ESPACIOS COMERCIALES:
            4️⃣ Ver los espacios comerciales disponibles en arrendamiento 🛍️📌
            5️⃣ Cómo participar en la subasta de arrendamiento 💼📊
            6️⃣ Fechas y requisitos para arrendamiento comercial 🗓️📋

            OTROS:
            7️⃣ Contacto 📞📩

            🔹 Escribe el número de la opción que deseas.
            🔹 Escribe "menú" para ver nuevamente las opciones.
            🔹 Escribe "salir" para cerrar el chat.
            MENU;
        }// Detectar salida
        elseif (Str::contains($comentario, ['salir','ADIOS','adios','Adios','Adiós', 'hasta luego','Hasta luego'])) {
            $respuesta = <<<SALIDA
            Gracias por contactarte con EMILIMA. 👋
            Si necesitas más información, no dudes en volver a escribirnos.
            ¡Que tengas un excelente día! ☀️
            SALIDA;
        }// Opción no válida
        else {
            $respuesta = <<<NO_OPCION
            Lo siento 😥, no entendí tu mensaje.
            Por favor, escribe "hola" o un número del 1 al 4 o escribe "menú" para ver las opciones disponibles.
            Escribe "salir" para cerrar el chat.
            NO_OPCION;
        }

        // Enviar mensaje
        $response = Http::withOptions($this->ws_responder_texto($numero, $respuesta))
            ->post($endpoint);

        if ($response->failed()) {
            Log::info("❌ Error al enviar el mensaje");
            Log::info($response->body());
        } else {
            Log::info("✅ Mensaje enviado correctamente");
            Log::info($response->body());
        }
    }

  function extraerValoresEnviarRespuesta($req)
  {
    try {
      $entry = $req['entry'][0];
      $changes = $entry['changes'][0];
      $value = $changes['value'];
      $from = $value['metadata']['display_phone_number'];

      if (isset($value['messages'][0]['interactive']['button_reply']['id'])) {
        $id_boton = $value['messages'][0]['interactive']['button_reply']['id'];

        if($id_boton=='registro'){
          Log::info('Se presiono el boton Registro');
        };

        if($id_boton=='ver_balance'){
          Log::info('Se presiono el boton Ver balance');
        };

        if($id_boton=='realizar_pago'){
          Log::info('Se presiono el boton Realizar pago');
        };

      }



      $objetomensaje = $value['messages'];
      $mensaje = $objetomensaje[0];

      $comentario = $mensaje['text']['body'];
      $numero = $mensaje['from'];
      $id = $mensaje['id'];
      $timestamp = $mensaje['timestamp'];

      $this->enviarRespuesta($comentario, $numero, $id, $timestamp, $from);

      $response = Response::make(json_encode(['message' => 'EVENT_RECEIVED']), 200);
      $response->header('Content-Type', 'application/json');
    } catch (Exception $e) {

      $response = Response::make(json_encode(['message' => 'EVENT_RECEIVED']), 200);
      $response->header('Content-Type', 'application/json');
    }
  }


  function escuchar(Request $request)
  {

    if ($request->isMethod('post')) {

      $data = $request->json()->all(); // Obtiene el JSON como array

      // Log::info($data);

      $this->extraerValoresEnviarRespuesta($data);

    } else {
        Log::info('No llegó nada');
    }

  }


  function token()
  {

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

      if (isset($_GET['hub_mode']) && isset($_GET['hub_verify_token']) && isset($_GET['hub_challenge']) && $_GET['hub_mode'] === 'subscribe' && $_GET['hub_verify_token'] === $this->ws_tokenwebhook) {

        Log::info($_GET['hub_mode']);
        Log::info($_GET['hub_verify_token']);
        Log::info($_GET['hub_challenge']);

        echo $_GET['hub_challenge'];
      } else {
        http_response_code(403);
        Log::info('Fallo...');
      }
    }
  }


  public function verificarEnv()
    {
        return response()->json([
            'ws_token' => $this->ws_token,
            'ws_endpoint' => $this->ws_endpoint,
            'ws_tokenwebhook' => $this->ws_tokenwebhook,
        ]);
    }


}
