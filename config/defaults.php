<?php

return [
  // tenat website default menus 
  'menus' => [
    [
      "text" => "Home",
      "href" => "",
      "icon" => "empty",
      "target" => "_self",
      "title" => "",
      "type" => "home",
    ],
    [
      "text" => "Properties",
      "href" => "",
      "icon" => "empty",
      "target" => "_self",
      "title" => "",
      "type" => "properties",
    ],
    [
      "text" => "Projects",
      "href" => "",
      "icon" => "empty",
      "target" => "_self",
      "title" => "",
      "type" => "projects",
    ],
    [
      "text" => "Team",
      "href" => "",
      "icon" => "empty",
      "target" => "_self",
      "title" => "",
      "type" => "agents",
    ],

    [
      "text" => "Blog",
      "href" => "",
      "icon" => "empty",
      "target" => "_self",
      "title" => "",
      "type" => "blog",
    ],
    [
      "text" => "About Us",
      "href" => "",
      "icon" => "empty",
      "target" => "_self",
      "title" => "",
      "type" => "about-us",
    ],
    [
      "text" => "FAQ",
      "href" => "",
      "icon" => "empty",
      "target" => "_self",
      "title" => "",
      "type" => "faq",
    ],
    [
      "text" => "Contact",
      "href" => "",
      "icon" => "empty",
      "target" => "_self",
      "title" => "",
      "type" => "contact",
    ]

  ],


  // child menus example
  // [
  //   "text" => "Shop",
  //   "href" => "",
  //   "icon" => "empty",
  //   "target" => "_self",
  //   "title" => "",
  //   "type" => "custom",
  //   "children" => [
  //     [
  //       "text" => "Products",
  //       "href" => "",
  //       "icon" => "empty",
  //       "target" => "_self",
  //       "title" => "",
  //       "type" => "products",
  //     ],
  //     [
  //       "text" => "Cart",
  //       "href" => "",
  //       "icon" => "empty",
  //       "target" => "_self",
  //       "title" => "",
  //       "type" => "cart",
  //     ],
  //   ],
  // ],
  // tenant default Daynamic Mail Templates
  'mailTemplates' => [

    [
      'mail_type' => "verify_email",
      'mail_subject' => "Verify Your Email Address",
      'mail_body' => "<p>Hi <b>{username}</b>,</p><p>We just need to verify your email address before you can access to your dashboard.</p><p>Verify your email address, {verification_link}.</p><p>Thank you.<br>{website_title}</p>",
    ],
    [
      'mail_type' => "reset_password",
      'mail_subject' => "Recover Password of Your Account",
      'mail_body' => '<p>Hi {customer_name},</p><p>We have received a request to reset your password. If you did not make the request, just ignore this email. Otherwise, you can reset your password using this below link.</p><p>{password_reset_link}</p><p>Thanks,<br>{website_title}</p>',

    ],

    // [
    //   'mail_type' => "payment_success",
    //   'mail_subject' => "Payment Success",
    //   'mail_body' => '<p>Hi {customer_name},</p><p>Your payment is completed. We have attached an invoice in this mail.<br />Invoice No: #{invoice_number}</p><p>Best regards.<br />{website_title}</p>',
    // ],
    // [
    //   'mail_type' => "payment_approved",
    //   'mail_subject' => "Payment Approved",
    //   'mail_body' => '<p>Hi {customer_name},</p><p>We have approved your payment. We have attached an invoice in this mail. You can see the details here.<br />Invoice No: #{invoice_number}</p><p>Best regards.<br />{website_title}</p>',
    // ],
    // [
    //   'mail_type' => "payment_rejected",
    //   'mail_subject' => "Payment Rejected",
    //   'mail_body' => '<p>Hi {customer_name},</p><p>We have rejected your payment. We have attached an invoice in this mail.</p><p>For any kind of query please contact us.</p><p>Support email: admin@gmail.com<br />Invoice No: #{invoice_number}</p><p>Best regards.<br />{website_title}</p>',
    // ],
    [
      'mail_type' => "agent_register",
      'mail_subject' => "An agent account is registered",
      'mail_body' => '<p>Hi {username},<br /><br />This is a confirmation mail from us.<br />Successfully created an agent account for you<br />please visit the website and log in to your account.</p>
<p><strong>Login Url: </strong>{login_url}<br /><strong>Username:</strong> {username}<br /><strong>Password:</strong> {password}<br /><br /></p>
<p>Best Regards,<br />{website_title}.</p>',
    ],

  ]
];
