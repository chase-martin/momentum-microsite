<?php

?>
<style>

    html, body { width : 100%; height : 100%; padding : 0; background : #fff; margin : 0; font-family : arial }

    a { text-decoration : none }

    .container { width : 262px; margin : 0 auto; padding-top : 200px; }

    #bar { width : 100%; height : 35px; padding : 15px 0; background : url(../images/bar.png) repeat-x; }

    #container { width : 960px; margin : 0 auto; }

        /*-------LOGIN STARTS HERE -------*/

        /* Login Container (default to float:right) */
    #loginContainer {
        position  : relative;
        float     : right;
        font-size : 12px;
        }

        /* Login Button */
    #loginButton {
        display            : inline-block;
        float              : right;
        background         : #d2e0ea url(../images/buttonbg.png) repeat-x;
        border             : 1px solid #899caa;
        border-radius      : 3px;
        -moz-border-radius : 3px;
        position           : relative;
        z-index            : 30;
        cursor             : pointer;
        }

        /* Login Button Text */
    #loginButton span {
        color       : #445058;
        font-size   : 14px;
        font-weight : bold;
        text-shadow : 1px 1px #fff;
        padding     : 7px 29px 9px 10px;
        background  : url(../images/loginArrow.png) no-repeat 53px 7px;
        display     : block
        }

    #loginButton:hover {
        background : url(../images/buttonbgHover.png) repeat-x;
        }

        /* Login Box */
    #loginBox {

        top      : 34px;
        right    : 0;

        z-index  : 29;
        }

        /* If the Login Button has been clicked */
    #loginButton.active {
        border-radius : 3px 3px 0 0;
        }

    #loginButton.active span {
        background-position : 53px -76px;
        }

        /* A Line added to overlap the border */
    #loginButton.active em {
        position   : absolute;
        width      : 100%;
        height     : 1px;
        background : #d2e0ea;
        bottom     : -1px;
        }

        /* Login Form */
    #loginForm {
        width              : 248px;
        border             : 1px solid #899caa;
        border-radius      : 3px 0 3px 3px;
        -moz-border-radius : 3px 0 3px 3px;
        margin-top         : -1px;
        background         : #d2e0ea;
        padding            : 6px;
        }

    #loginForm fieldset {
        margin  : 0 0 12px 0;
        display : block;
        border  : 0;
        padding : 0;
        }

    fieldset#body {
        background         : #fff;
        border-radius      : 3px;
        -moz-border-radius : 3px;
        padding            : 10px 13px;
        margin             : 0;
        }

    #loginForm #checkbox {
        width   : auto;
        margin  : 1px 9px 0 0;
        float   : left;
        padding : 0;
        border  : 0;
        *margin : -3px 9px 0 0; /* IE7 Fix */
        }

    #body label {
        color   : #3a454d;
        margin  : 9px 0 0 0;
        display : block;
        float   : left;
        }

    #loginForm #body fieldset label {
        display : block;
        float   : none;
        margin  : 0 0 6px 0;
        }

        /* Default Input */
    #loginForm input {
        width              : 92%;
        border             : 1px solid #899caa;
        border-radius      : 3px;
        -moz-border-radius : 3px;
        color              : #3a454d;
        font-weight        : bold;
        padding            : 8px 8px;
        box-shadow         : inset 0px 1px 3px #bbb;
        -webkit-box-shadow : inset 0px 1px 3px #bbb;
        -moz-box-shadow    : inset 0px 1px 3px #bbb;
        font-size          : 12px;
        }

        /* Sign In Button */
    #loginForm #login {
        width              : auto;
        float              : left;
        background         : #339cdf url(../images/loginbuttonbg.png) repeat-x;
        color              : #fff;
        padding            : 7px 10px 8px 10px;
        text-shadow        : 0px -1px #278db8;
        border             : 1px solid #339cdf;
        box-shadow         : none;
        -moz-box-shadow    : none;
        -webkit-box-shadow : none;
        margin             : 0 12px 0 0;
        cursor             : pointer;
        *padding           : 7px 2px 8px 2px; /* IE7 Fix */
        }

        /* Forgot your password */
    #loginForm span {
        text-align : center;
        display    : block;
        padding    : 7px 0 4px 0;
        }

    #loginForm span a {
        color       : #3a454d;
        text-shadow : 1px 1px #fff;
        font-size   : 12px;
        }

    input:focus {
        outline : none;
        }
</style>
<?php
$username  = $_SESSION['CMS_USER']['username'];
?>
<div id="loginBox">
    <h2><?php echo $username ?></h2>
    <?php if (empty($username)) { ?>
    <form id="loginForm" method="POST" action="/cms/login" >
        <fieldset id="body">
            <fieldset>
                <label for="username">Username</label>
                <input type="text" name="username" id="email"/>
            </fieldset>
            <fieldset>
                <label for="password">Password</label>
                <input type="password" name="password" id="password"/>
            </fieldset>
            <input type="submit" id="login" value="Sign in"/>

        </fieldset>

    </form>
    <?php } else { ?>
        <form id="loginForm" method="POST" action="/cms/login" >

                    <input type="hidden" name="logout" id="logout" value="1"/>


                <input type="submit" id="login" value="Logout"/>

            </fieldset>

        </form>
    <?php } ?>
</div>