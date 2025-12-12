<?php
return [
    'encoding' => 'UTF-8',
    'finalize' => true,
    'cachePath' => null,
    'settings' => [
        'default' => [
            'HTML.Allowed' => 'div,b,strong,i,em,u,a[href|title],ul,ol,li,p[style],br,span,img[src|alt]',
            'Cache.SerializerPath' => null,
        ],
        'youtube' => [
            'HTML.SafeIframe' => 'true',
            'URI.SafeIframeRegexp' => "%^(https?:)?(//)?((www\.)?youtube(-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%",
            'HTML.Allowed' => 'iframe[src|width|height|frameborder],div,b,strong,i,em,u,a[href|title],ul,ol,li,p[style],br,span,img[src|alt]',
            'Cache.SerializerPath' => null,
        ],
    ],
];
