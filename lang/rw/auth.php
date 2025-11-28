<?php

return [
    // Authentication messages
    'login_success' => 'Kwinjira byagenze neza',
    'register_success' => 'Kwiyandikisha byagenze neza',
    'logout_success' => 'Gusohoka byagenze neza',
    'password_changed' => 'Ijambo ry\'ibanga ryahinduwe neza',
    'failed' => 'Ibyangombwa ntibihuye n\'ibyacu.',
    'password' => 'Ijambo ry\'ibanga ryoherejwe siryo.',
    'throttle' => 'Kugerageza kwinjira kenshi. Subira gerageza mu :seconds.',
    'invalid_credentials' => 'Imyirondoro ntabwo ari yo',
    'email_in_use' => 'Iyi imeri isanzwe ikoreshwa',
    'invalid_current_password' => 'Ijambo ry\'ibanga ubu ririmo ntabwo ari ryo',
    'forgot_password' => 'Wibagiwe ijambo ry\'ibanga?',
    'remember_me' => 'Nyibuka',
    
    // Login page
    'login' => [
        'title' => 'Murakaza neza',
        'subtitle' => 'Injira muri konti yawe',
        'email' => 'Imeri',
        'password' => 'Ijambo ry\'ibanga',
        'login_button' => 'Injira',
        'no_account' => 'Ntago ufite konti?',
        'forgot_password' => 'Wibagiwe ijambo ry\'ibanga?',
        'sign_up' => 'Iyandikishe',
        'logging_in' => 'Kwinjira...',
    ],
    
    // Google Sign In
    'continue_with_google' => 'Koresha Konti ya Google',
    'or_sign_in_with_email' => 'Cg ukoreshe imeri',
    'or_sign_up_with_email' => 'Cg wiyandikishe ukoresheje imeri',
    
    // Common
    'email' => 'Imeri',
    'name' => 'Izina ryuzuye',
    'confirm_password' => 'Emeza ijambo ry\'ibanga',
    'already_registered' => 'Ubu ufite konti?',
    'sign_in' => 'Injira',
    
    // Register page
    // Forgot Password Page
    'forgot_password_page' => [
        'title' => 'Guhindura ijambo ry\'ibanga',
        'subtitle' => 'Andika imeri twoherezaho uburyo bwo guhindura ijambo ry\'ibanga.',
        'email_placeholder' => 'Andika imeri yawe',
        'submit_button' => 'Hindura ijambo ry\'ibanga',
        'back_to_login' => 'Subira kwinjira',
        'success_message' => 'Twohereje imeri y\'uburyo bwo guhindura ijambo ry\'ibanga!',
        'sending' => 'Biri gukorwa...',
        'dialog_title' => 'Reba Imeri Yawe',
        'dialog_message' => 'Twohereje ihuza ryo guhindura ijambo ry\'ibanga kuri:',
        'dialog_button' => 'Nayibonye',
    ],

    // Password Reset Email
    'reset_password_email' => [
        'title' => 'Guhindura ijambo ry\'ibanga',
        'subtitle' => 'Mwasabye guhindura ijambo ry\'ibanga rya konti yanyu',
        'you_are_receiving' => 'Muri kubona iyi imeri kuko twakiriye gahunda yo kuhindura ijambo ry\'ibanga kuri konte yanyu.',
        'reset_button' => 'Hindura ijambo ry\'ibanga',
        'expiry_notice' => 'Iyi mbuga yo kuhindura ijambo ry\'ibanga irarangira mu minota :count.',
        'ignore_if_not_requested' => 'Niba mutigeze musaba ko mwahindura ijambo ry\'ibanga, mwakwirengagiza ubu butumwa.',
        'contact_support' => 'Niba mufite ikibazo cy\'uko mwakiriye ubu butumwa, nyamuneka twandikire kuri :email.',
        'trouble_with_button' => 'Nugira ikibazo cyo gukoresha buto ya \"Hindura ijambo ry\'ibanga\", kopiya iri huza mu urubuga rwawe.',
    ],

    // Reset Password Page
    'reset_password_page' => [
        'title' => 'Shyiraho ijambo ry\'ibanga rishya',
        'subtitle' => 'Shyiraho ijambo ry\'ibanga rishya kuri konte yawe.',
        'email_placeholder' => 'Imeri yawe',
        'new_password_placeholder' => 'Andika ijambo ry\'ibanga rishya',
        'confirm_password_placeholder' => 'Emeza ijambo ry\'ibanga rishya',
        'submit_button' => 'Hindura ijambo ry\'ibanga',
        'back_to_login' => 'Subira kwinjira',
        'success_message' => 'Ijambo ry\'ibanga ryawe ryahinduwe neza!',
    ],

    'register' => [
        'title' => 'Kora Konti Yawe',
        'subtitle' => 'Injira muri MK Scholars Driving School uyu munsi!',
        'first_name' => 'Izina ribanza',
        'last_name' => 'Izina rya nyina',
        'email' => 'Imeri',
        'sign_up' => 'Iyandikishe',
        'phone' => 'Numero ya telefone',
        'password' => 'Ijambo ry\'ibanga',
        'confirm_password' => 'Emeza ijambo ry\'ibanga',
        'language' => 'Ururimi ukunda',
    'creating_account' => 'Biri gukorwa...',
        'register_button' => 'Kora Konti',
        'have_account' => 'Ubu ufite konte?',
        'sign_in' => 'Injira'
    ],
    
    // Errors
    'errors' => [
        'invalid_credentials' => 'Imeri cyangwa ijambo ry\'ibanga ntabwo aribyo',
        'email_exists' => 'Konti isanzweho. Mujye aho binjirira.',
        'password_mismatch' => 'Amajambo y\'ibanga ntabwo arahuje',
        'weak_password' => 'Ijambo ry\'ibanga kagomba kuba inyuguti 6 byibura',
        'invalid_email' => 'Andika imeri yemewe'
    ],
    
    // Password validation messages
    'password_requirements' => [
        'title' => 'Ijambo ry\'ibanga kagomba kuba rifite:',
        'length' => 'Inyuguti 8 byibura',
        'uppercase' => 'Inyuguti nkuru imwe byibura',
        'lowercase' => 'Inyuguti nto imwe byibura',
        'number' => 'Umubare umwe byibura',
        'special' => 'Inyuguti idasanzwe imwe (!@#$%^&*) byibura',
        'strength' => [
            'weak' => 'Ntarengwa',
            'medium' => 'Iragerageza',
            'strong' => 'Ikomeye',
            'very_strong' => 'Ikomeye cyane'
        ],
        'match' => 'Amajambo y\'ibanga arahuje',
        'mismatch' => 'Amajambo y\'ibanga ntabwo arahuje'
    ],

    // Forgot Password page
    'forgot_password_page' => [
        'title' => 'Hindura ijambo ry\'ibanga',
        'subtitle' => 'Andika imeri ifasha mu guhindura ijambo ry\'ibanga.',
        'email_placeholder' => 'Imeri',
        'submit_button' => 'Ohereza',
        'back_to_login' => 'Subira kwinjira',
        'success_message' => 'Twohereje imeri ifite umurongo wo guhinduriraho ijambo ry\'ibanga!',
        'error_message' => 'Ntago tubona konti ifite iyo imeri.'
    ],
];