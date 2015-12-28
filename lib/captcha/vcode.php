<?php
require 'class.captcha.php';//引用路径
Captcha::$useImgBg = false;  // 是否使用背景图片
Captcha::$useNoise = false; // 是否添加杂点 
Captcha::$useCurve = false; // 是否绘制干扰线 
Captcha::$useZh    = false; // 是否使用中文验证码 
Captcha::$fontSize = 15;   // 验证码字体大小(像素) 
Captcha::$length   = 4;    // 验证码字符数
Captcha::entry();  // 输出图片