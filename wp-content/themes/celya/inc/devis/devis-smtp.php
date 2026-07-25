<?php
/**
 * Devis — Envoi des emails via SMTP Brevo
 *
 * Configure PHPMailer (utilisé par wp_mail) pour passer par le relais SMTP de
 * Brevo lorsque l'option est activée dans les réglages. Améliore la
 * délivrabilité des notifications de demande de devis.
 *
 * @package Celya
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Applique la configuration SMTP Brevo à PHPMailer.
 *
 * @param PHPMailer\PHPMailer\PHPMailer $phpmailer Instance PHPMailer (par référence).
 */
function celya_devis_configure_smtp( $phpmailer ) {

    if ( '1' !== get_option( 'celya_devis_smtp_enabled' ) ) {
        return;
    }

    $host = get_option( 'celya_devis_smtp_host', 'smtp-relay.brevo.com' );
    $user = get_option( 'celya_devis_smtp_user', '' );
    $pass = get_option( 'celya_devis_smtp_pass', '' );

    // Sans hôte ni identifiants, on laisse WordPress utiliser l'envoi par défaut.
    if ( ! $host || ! $user || ! $pass ) {
        return;
    }

    $port = (int) get_option( 'celya_devis_smtp_port', 587 );

    $phpmailer->isSMTP();
    $phpmailer->Host        = $host;
    $phpmailer->Port        = $port;
    $phpmailer->SMTPAuth    = true;
    $phpmailer->Username    = $user;
    $phpmailer->Password    = $pass;
    // 587 → STARTTLS (tls) ; 465 → SMTPS (ssl).
    $phpmailer->SMTPSecure  = ( 465 === $port ) ? 'ssl' : 'tls';

    $from_email = get_option( 'celya_devis_smtp_from_email', '' );
    $from_name  = get_option( 'celya_devis_smtp_from_name', get_bloginfo( 'name' ) );

    if ( $from_email && is_email( $from_email ) ) {
        $phpmailer->setFrom( $from_email, $from_name, false );
        $phpmailer->Sender = $from_email; // Return-Path.
    }
}
add_action( 'phpmailer_init', 'celya_devis_configure_smtp' );

/**
 * Force l'adresse expéditeur configurée (cohérence avec Brevo).
 *
 * @param string $email Email actuel.
 * @return string
 */
function celya_devis_mail_from( $email ) {
    if ( '1' !== get_option( 'celya_devis_smtp_enabled' ) ) {
        return $email;
    }
    $from = get_option( 'celya_devis_smtp_from_email', '' );
    return ( $from && is_email( $from ) ) ? $from : $email;
}
add_filter( 'wp_mail_from', 'celya_devis_mail_from' );

/**
 * Force le nom expéditeur configuré.
 *
 * @param string $name Nom actuel.
 * @return string
 */
function celya_devis_mail_from_name( $name ) {
    if ( '1' !== get_option( 'celya_devis_smtp_enabled' ) ) {
        return $name;
    }
    $from = get_option( 'celya_devis_smtp_from_name', '' );
    return $from ? $from : $name;
}
add_filter( 'wp_mail_from_name', 'celya_devis_mail_from_name' );
