<?php 
    function set_up_session_settings() {
        ini_set("session.cookie_lifetime", 0);
        ini_set("session.use_cookies", 1);
        ini_set("session.use_only_cookies", 1);
        ini_set("session.use_strict_mode", 1);
        ini_set("session.cookie_httponly", 1);
        ini_set("session.cookie_secure", 1);
        ini_set("session.cookie_samesite", "Strict");
        ini_set("session.gc_maxlifetime", 86400);
        ini_set("session.use_trans_id", 0);
        ini_set("session.referer_check", "nocache");
        ini_set("session.sid_length", 48);
        ini_set("session.sid_bits_per_character", 6);
        ini_set("session.hash_function", "sha256");
    }
?>