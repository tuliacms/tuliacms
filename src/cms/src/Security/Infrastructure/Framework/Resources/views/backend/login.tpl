{% assets ['backend'] %}
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    {{ theme_head() }}
    <meta name="robots" content="noindex,nofollow">
    <title>{% block title %}Tulia CMS - Administration Panel login{% endblock %}</title>
    {% block head %}{% endblock %}
    <style>
        body {
            background: #27343E;
            color: #fff;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
            font-family: "Poppins", sans-serif;
            overflow: hidden;
        }
        body .vertical-centered-box {
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
        }
        body .vertical-centered-box:after {
            content: '';
            display: inline-block;
            height: 100%;
            vertical-align: middle;
            margin-right: -0.25em;
        }

        .body-container {
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
            display: flex;
        }
        .body-container .left-side {
            flex: 1 1 auto;
            max-width: 100%;
            position: relative;
            overflow: hidden;
        }
        .body-container .left-side:before {
            content: "";
            display: block;
            right: -10px;
            top: 0;
            bottom: 0;
            width: 10px;
            box-shadow: 0 0 14px rgba(0,0,0,0.8);
            position: absolute;
            z-index: 10000;
        }
        .body-container .left-side .motivation-quote {
            position: absolute;
            right: 25px;
            bottom: 25px;
            z-index: 1000;
            font-size: 22px;
            color: #fff;
            text-align: right;
            text-shadow: 0 0 10px rgba(0,0,0,.5);
        }
        .body-container .left-side .slogan {
            position: absolute;
            left: 50%;
            top: 190px;
            transform: translateX(-50%);
            z-index: 1000;
            font-size: 50px;
            white-space: nowrap;
            color: #fff;
            font-weight: bold;
            text-shadow: 0 0 10px rgba(0,0,0,.5);
        }
        .body-container .left-side .background-image {
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
            z-index: 1;
            background-position: center center;
            background-repeat: no-repeat;
            background-size: cover;
            opacity: 0;
            transition: .12s all;
        }
        .body-container .left-side .background-image.active {
            opacity: 1;
        }

        .body-container .right-side {
            flex: 0 0 600px;
            max-width: 600px;
            position: relative;
            background-color: #27343E;
        }

        .image-copyrights {
            position: absolute;
            left: 15px;
            bottom: 15px;
            z-index: 1000;
            color: rgba(255,255,255,.5);
        }
        .image-copyrights a {
            color: rgba(255,255,255,.5);
        }


        #viewbox-loader {
            text-align: center;
        }
        .loader-content {
            box-sizing: border-box;
            display: inline-block;
            vertical-align: middle;
            font-size: 0;
        }
        .loader-circle {
            position: absolute;
            left: 50%;
            top: 50%;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .1);
            margin-left: -60px;
            margin-top: -60px;
        }
        .loader-line-mask {
            position: absolute;
            left: 50%;
            top: 50%;
            width: 60px;
            height: 120px;
            margin-left: -60px;
            margin-top: -60px;
            overflow: hidden;
            transform-origin: 60px 60px;
            -webkit-mask-image: -webkit-linear-gradient(top, #fff, rgba(255, 255, 255, 0));
            animation: rotate 1.2s infinite linear;
        }
        .loader-line-mask .loader-line {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.8);
        }

        @keyframes rotate {
            0% {
                -webkit-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -ms-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -ms-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        .centered-element {position:absolute;left:50%;top:50%;width:370px;transform:translate(-50%,-50%);}
        .box {text-align:left;color:#222;display:block}
        .box .box-body {padding:30px;}
        .box .box-footer {padding:13px 30px 15px;border-top:1px solid rgba(255,255,255,.1);}
        .box .box-footer a {font-size:12px;transition:.12s all;}
        .box .logo {font-size:27px;margin:10px 0 20px 0;color:#fff;font-weight:bold;}
        .box .logo img {display:block;max-width:100%;width:180px;}
        .box .logo-slogan {margin-bottom:30px}
        .box .btn {box-shadow:none;font-size:12px;padding-left:25px;padding-right:25px;}
        .box .btn-link {font-size:12px;color:#fff;opacity:.8;}
        .box p {color:#fff;}

        .credit-links {position:absolute;right:0;left:0;bottom:15px;z-index:1000;text-align:center}
        .credit-links a {color:rgba(255,255,255,.5);font-size:13px;font-weight:300;}

        .viewbox {opacity:0;z-index:1;transition:.12s all;}
        .viewbox.active {z-index:10;opacity:1;}
    </style>
    <script nonce="{{ csp_nonce() }}">
        let bgImages = {{ bgImages|json_encode|raw }};
        let Login = function () {
            this.viewbox = null;
            this.animationTime = 500;

            this.init = function () {
                let self = this;

                $('[data-show-viewbox]').click(function (e) {
                    e.preventDefault();
                    self.showViewbox($(this).attr('data-show-viewbox'));
                });

                $('.viewbox').css('transition', this.animationTime + 'ms all');

                this.showViewbox('viewbox-login');

                $('.login-btn').click(function (e) {
                    //e.preventDefault();

                    self.showViewbox('viewbox-loader');

                    /*if($('#login-username').val() == '')
                    {
                        setTimeout(function () {
                            $('#viewbox-login .centered-element .alert').remove();
                            $('#viewbox-login .centered-element').prepend('<div class="alert alert-warning">Wprowadzone dane są nieprawidłowe.</div>');
                            self.showViewbox('viewbox-login');
                        }, 1000);
                    }
                    else
                    {
                        setTimeout(function () {
                            document.location.href = '/gestione-layout/dashboard.php';
                        }, 1000);
                    }*/
                });
            };

            this.showViewbox = function (id) {
                if (this.viewbox) {
                    $('#' + this.viewbox).removeClass('active');
                }

                let cont = $('#' + id);

                cont.addClass('active');

                let inputs = cont.find('.form-control-autofocus');
                let focused = false;

                inputs.each(function () {
                    if (focused) {
                        return;
                    }

                    if ($(this).val() === '') {
                        $(this).trigger('focus');
                        focused = true;
                    }
                });

                this.viewbox = id;
            };

            this.init();
        };

        let prefetchImage = function () {
            let image = new Image;
            image.onload = function () {
                $('.background-image').css('background-image', 'url(' + this.src + ')').addClass('active');
            };

            let modificator = (new Date).getTime();
            let randomImage = bgImages[Math.floor(Math.random() * bgImages.length)];
            image.src = randomImage.path + '?ts=' + modificator;
        };

        prefetchImage();

        $(function () {
            new Login;
        });
    </script>
</head>
<body>
{% block beforebody %}{% endblock %}
<div class="body-container">
    <div class="left-side">
        <div class="slogan">Unleash Your Creativity</div>
        <div class="motivation-quote">
            <p>Kreatywność jest największym darem ludzkiej inteligencji.</p>
            <p class="quote-author">Ken Robinson</p>
        </div>
        <div class="background-image"></div>
        <div class="image-copyrights">Images by <a href="https://unsplash.com/" target="_blank" rel="noopener noreferer">unsplash.com</a></div>
    </div>
    <div class="right-side">
        <div class="vertical-centered-box viewbox" id="viewbox-loader">
            <div class="loader-content">
                <div class="loader-circle"></div>
                <div class="loader-line-mask">
                    <div class="loader-line"></div>
                </div>
                <svg class="loader-logo" width="40px" height="40px" viewBox="0 0 200 241.29" xmlns="http://www.w3.org/2000/svg">
                    <g transform="matrix(8.0645 0 0 8.0645 -4.5161 -80.645)" fill="#fff" featurekey="nameFeature-0">
                        <path d="m6.08 10q0.6 0 1.02 0.42t0.42 1.02v2.92q0 0.6-0.42 1.02t-1.02 0.42h-4.12q-0.56 0-0.98-0.42t-0.42-1.02v-2.92q0-0.6 0.42-1.02t0.98-0.42h4.12zm17.88 0q0.56 0 0.98 0.42t0.42 1.02v2.92q0 0.6-0.42 1.02t-0.98 0.42h-8.04v22.72q0 0.56-0.42 0.98t-0.98 0.42h-3.08q-0.6 0-1.02-0.42t-0.42-0.98v-27.08q0-0.6 0.42-1.02t1.02-0.42h12.52z"/>
                    </g>
                </svg>
            </div>
        </div>
        <div class="vertical-centered-box viewbox" id="viewbox-login">
            <div class="centered-element">
                <div class="box">
                    <div class="box-body">
                        <div class="logo">
                            <img class="logo-image" src="{{ asset('/assets/core/backend/theme/images/logo.svg') }}" alt="Tulia CMS" />
                        </div>
                        <p class="logo-slogan">Login to Your Administration Panel.</p>
                        {{ flashes() }}
                        {% if error %}
                            <div class="alert alert-danger">{{ error.messageKey|trans(error.messageData, 'auth') }}</div>
                        {% endif %}
                        <form action="{{ path('backend.login.process') }}" method="POST">
                            <input type="hidden" name="_token" value="{{ csrf_token('authenticate') }}" />
                            <fieldset class="form-group">
                                <label class="d-none">{{ 'username'|trans }}</label>
                                <div class="input-group mb-1">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control form-control-autofocus" id="username" name="username" value="{{ last_username }}" placeholder="{{ 'username'|trans }}" />
                                </div>
                            </fieldset>
                            <fieldset class="form-group">
                                <label class="d-none">{{ 'password'|trans }}</label>
                                <div class="input-group m-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input type="password" class="form-control form-control-autofocus" id="password" name="password" placeholder="{{ 'password'|trans }}" />
                                </div>
                            </fieldset>
                            <button type="submit" class="btn btn-primary login-btn">{{ 'signIn'|trans }}</button>
                            <a href="#" class="btn btn-link" data-show-viewbox="viewbox-password-remember">Forgot password?</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="vertical-centered-box viewbox" id="viewbox-password-remember">
            <div class="centered-element">
                <div class="box">
                    <div class="box-body">
                        <div class="logo">Tulia CMS</div>
                        <p class="logo-slogan">Insert Your e-mail address to restore password.</p>
                        <fieldset class="form-group">
                            <label class="d-none">Username</label>
                            <div class="input-group mb-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text" class="form-control form-control-autofocus" placeholder="Username">
                            </div>
                        </fieldset>
                        <a href="#" class="btn btn-primary login-btn">Process</a>
                        <a href="#" class="btn btn-link" data-show-viewbox="viewbox-login">Sign in</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="credit-links">
            <a href="#">Created with &#10084; by Adam Banaszkiewicz</a>
        </div>
    </div>
</div>
{# <div class="login-form">
    <div class="container h-100">
        <div class="row align-items-center h-100">
            <div class="col-6 mx-auto">
                <div class="card">
                    <div class="card-body">
                        {% if errors %}
                            {% for message in errors %}
                                <div class="alert alert-danger">{{ message }}</div>
                            {% endfor %}
                        {% endif %}
                        <form action="?returnPath={{ returnPath }}" method="POST">
                            <div class="form-group">
                                <label for="username">{{ 'username'|trans }}</label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="{{ 'username'|trans }}">
                            </div>
                            <div class="form-group">
                                <label for="password">{{ 'password'|trans }}</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="{{ 'password'|trans }}">
                            </div>
                            <button type="submit" class="btn btn-primary">{{ 'login'|trans }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> #}

{#
<div id="particles-background" class="vertical-centered-box"></div>
<div id="particles-foreground" class="vertical-centered-box"></div>
<div class="vertical-centered-box viewbox" id="viewbox-loader">
    <div class="content">
        <div class="loader-circle"></div>
        <div class="loader-line-mask">
            <div class="loader-line"></div>
        </div>
        <svg width="36px" height="24px" viewBox="0 0 36 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <path d="M8.98885921,23.8523026 C8.8942483,23.9435442 8.76801031,24 8.62933774,24 L2.03198365,24 C1.73814918,24 1.5,23.7482301 1.5,23.4380086 C1.5,23.2831829 1.55946972,23.1428989 1.65570253,23.0416777 L13.2166154,12.4291351 C13.3325814,12.3262031 13.4061076,12.1719477 13.4061076,11.999444 C13.4061076,11.8363496 13.3401502,11.6897927 13.2352673,11.587431 L1.68841087,0.990000249 C1.57298556,0.88706828 1.5,0.733668282 1.5,0.561734827 C1.5,0.251798399 1.73814918,2.85130108e-05 2.03198365,2.85130108e-05 L8.62933774,2.85130108e-05 C8.76855094,2.85130108e-05 8.89532956,0.0561991444 8.98994048,0.148296169 L21.4358709,11.5757407 C21.548593,11.6783875 21.6196864,11.8297916 21.6196864,11.999444 C21.6196864,12.1693815 21.5483227,12.3219261 21.4350599,12.4251432 L8.98885921,23.8523026 Z M26.5774333,23.8384453 L20.1765996,17.9616286 C20.060093,17.8578413 19.9865669,17.703871 19.9865669,17.5310822 C19.9865669,17.3859509 20.0390083,17.2536506 20.1246988,17.153855 L23.4190508,14.1291948 C23.5163648,14.0165684 23.6569296,13.945571 23.8131728,13.945571 C23.9602252,13.945571 24.0929508,14.0082997 24.1894539,14.1092357 L33.861933,22.9913237 C33.9892522,23.0939706 34.0714286,23.2559245 34.0714286,23.4381226 C34.0714286,23.748059 33.8332794,23.9998289 33.5394449,23.9998289 L26.9504707,23.9998289 C26.8053105,23.9998289 26.6733958,23.9382408 26.5774333,23.8384453 Z M26.5774333,0.161098511 C26.6733958,0.0615881034 26.8053105,0 26.9504707,0 L33.5394449,0 C33.8332794,0 34.0714286,0.251769886 34.0714286,0.561706314 C34.0714286,0.743904453 33.9892522,0.905573224 33.861933,1.00822006 L24.1894539,9.89030807 C24.0929508,9.99152926 23.9602252,10.0542579 23.8131728,10.0542579 C23.6569296,10.0542579 23.5163648,9.98354562 23.4190508,9.87063409 L20.1246988,6.8459739 C20.0390083,6.74617837 19.9865669,6.613878 19.9865669,6.46874677 C19.9865669,6.29624305 20.060093,6.14198767 20.1765996,6.03848544 L26.5774333,0.161098511 Z" fill="#FFFFFF"></path>
        </svg>
    </div>
</div>
<div class="vertical-centered-box viewbox" id="viewbox-login">
    <div class="centered-element">
        <div class="box">
            <div class="box-body">
                <div class="logo">Tulia CMS</div>

                {% if error %}
                    <div class="alert alert-danger">{{ error.messageKey|trans(error.messageData, 'security') }}</div>
                {% endif %}

                <form action="" method="POST">
                    <input type="hidden" name="_csrf_token" value="{{ csrf_token('authenticate') }}" />
                    <fieldset class="form-group">
                        <label class="d-none">{{ 'username'|trans }}</label>
                        <div class="input-group mb-1">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                            </div>
                            <input type="text" class="form-control" id="username" name="username" value="{{ last_username }}" placeholder="{{ 'username'|trans }}">
                        </div>
                    </fieldset>
                    <fieldset class="form-group">
                        <label class="d-none">{{ 'password'|trans }}</label>
                        <div class="input-group m-0">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                            </div>
                            <input type="password" class="form-control" id="password" name="password" placeholder="{{ 'password'|trans }}">
                        </div>
                    </fieldset>
                    <button type="submit" class="btn btn-primary login-btn">{{ 'login'|trans }}</button>
                </form>
            </div>
            <div class="box-footer text-center">
                <a href="#" data-show-viewbox="viewbox-password-remember">Zapomniałeś hasła?</a>
            </div>
        </div>
    </div>
</div>
<div class="vertical-centered-box viewbox" id="viewbox-password-remember">
    <div class="centered-element">
        <div class="box">
            <div class="box-body">
                <div class="logo">Tulia CMS</div>
                <p class="text-center">Wprowadź adres e-mail by przywrócić hasło.</p>
                <fieldset class="form-group">
                    <label class="d-none">Adres e-mail</label>
                    <div class="input-group mb-1">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control form-control-autofocus" placeholder="Adres e-mail">
                    </div>
                </fieldset>
                <a href="#" class="btn btn-primary login-btn">Przywróć hasło</a>
            </div>
            <div class="box-footer text-center">
                <a href="#" data-show-viewbox="viewbox-login">Zaloguj się</a>
            </div>
        </div>
    </div>
</div>
<div class="credit-links">
    <a href="#">Created with &#10084; by Adam Banaszkiewicz</a>
</div>
<script src="/assets/core/js-cookie/js.cookie-2.2.0.min.js"></script>

<script nonce="{{ csp_nonce() }}">
    /*!
     * Particleground
     *
     * @author Jonathan Nicol - @mrjnicol
     * @version 1.1.0
     * @description Creates a canvas based particle system background
     *
     * Inspired by http://requestlab.fr/ and http://disruptivebydesign.com/
     */
    !function(a,b){"use strict";function c(a){a=a||{};for(var b=1;b<arguments.length;b++){var c=arguments[b];if(c)for(var d in c)c.hasOwnProperty(d)&&("object"==typeof c[d]?deepExtend(a[d],c[d]):a[d]=c[d])}return a}function d(d,g){function h(){if(y){r=b.createElement("canvas"),r.className="pg-canvas",r.style.display="block",d.insertBefore(r,d.firstChild),s=r.getContext("2d"),i();for(var c=Math.round(r.width*r.height/g.density),e=0;c>e;e++){var f=new n;f.setStackPos(e),z.push(f)}a.addEventListener("resize",function(){k()},!1),b.addEventListener("mousemove",function(a){A=a.pageX,B=a.pageY},!1),D&&!C&&a.addEventListener("deviceorientation",function(){F=Math.min(Math.max(-event.beta,-30),30),E=Math.min(Math.max(-event.gamma,-30),30)},!0),j(),q("onInit")}}function i(){r.width=d.offsetWidth,r.height=d.offsetHeight,s.fillStyle=g.dotColor,s.strokeStyle=g.lineColor,s.lineWidth=g.lineWidth}function j(){if(y){u=a.innerWidth,v=a.innerHeight,s.clearRect(0,0,r.width,r.height);for(var b=0;b<z.length;b++)z[b].updatePosition();for(var b=0;b<z.length;b++)z[b].draw();G||(t=requestAnimationFrame(j))}}function k(){i();for(var a=d.offsetWidth,b=d.offsetHeight,c=z.length-1;c>=0;c--)(z[c].position.x>a||z[c].position.y>b)&&z.splice(c,1);var e=Math.round(r.width*r.height/g.density);if(e>z.length)for(;e>z.length;){var f=new n;z.push(f)}else e<z.length&&z.splice(e);for(c=z.length-1;c>=0;c--)z[c].setStackPos(c)}function l(){G=!0}function m(){G=!1,j()}function n(){switch(this.stackPos,this.active=!0,this.layer=Math.ceil(3*Math.random()),this.parallaxOffsetX=0,this.parallaxOffsetY=0,this.position={x:Math.ceil(Math.random()*r.width),y:Math.ceil(Math.random()*r.height)},this.speed={},g.directionX){case"left":this.speed.x=+(-g.maxSpeedX+Math.random()*g.maxSpeedX-g.minSpeedX).toFixed(2);break;case"right":this.speed.x=+(Math.random()*g.maxSpeedX+g.minSpeedX).toFixed(2);break;default:this.speed.x=+(-g.maxSpeedX/2+Math.random()*g.maxSpeedX).toFixed(2),this.speed.x+=this.speed.x>0?g.minSpeedX:-g.minSpeedX}switch(g.directionY){case"up":this.speed.y=+(-g.maxSpeedY+Math.random()*g.maxSpeedY-g.minSpeedY).toFixed(2);break;case"down":this.speed.y=+(Math.random()*g.maxSpeedY+g.minSpeedY).toFixed(2);break;default:this.speed.y=+(-g.maxSpeedY/2+Math.random()*g.maxSpeedY).toFixed(2),this.speed.x+=this.speed.y>0?g.minSpeedY:-g.minSpeedY}}function o(a,b){return b?void(g[a]=b):g[a]}function p(){console.log("destroy"),r.parentNode.removeChild(r),q("onDestroy"),f&&f(d).removeData("plugin_"+e)}function q(a){void 0!==g[a]&&g[a].call(d)}var r,s,t,u,v,w,x,y=!!b.createElement("canvas").getContext,z=[],A=0,B=0,C=!navigator.userAgent.match(/(iPhone|iPod|iPad|Android|BlackBerry|BB10|mobi|tablet|opera mini|nexus 7)/i),D=!!a.DeviceOrientationEvent,E=0,F=0,G=!1;return g=c({},a[e].defaults,g),n.prototype.draw=function(){s.beginPath(),s.arc(this.position.x+this.parallaxOffsetX,this.position.y+this.parallaxOffsetY,g.particleRadius/2,0,2*Math.PI,!0),s.closePath(),s.fill(),s.beginPath();for(var a=z.length-1;a>this.stackPos;a--){var b=z[a],c=this.position.x-b.position.x,d=this.position.y-b.position.y,e=Math.sqrt(c*c+d*d).toFixed(2);e<g.proximity&&(s.moveTo(this.position.x+this.parallaxOffsetX,this.position.y+this.parallaxOffsetY),g.curvedLines?s.quadraticCurveTo(Math.max(b.position.x,b.position.x),Math.min(b.position.y,b.position.y),b.position.x+b.parallaxOffsetX,b.position.y+b.parallaxOffsetY):s.lineTo(b.position.x+b.parallaxOffsetX,b.position.y+b.parallaxOffsetY))}s.stroke(),s.closePath()},n.prototype.updatePosition=function(){if(g.parallax){if(D&&!C){var a=(u-0)/60;w=(E- -30)*a+0;var b=(v-0)/60;x=(F- -30)*b+0}else w=A,x=B;this.parallaxTargX=(w-u/2)/(g.parallaxMultiplier*this.layer),this.parallaxOffsetX+=(this.parallaxTargX-this.parallaxOffsetX)/10,this.parallaxTargY=(x-v/2)/(g.parallaxMultiplier*this.layer),this.parallaxOffsetY+=(this.parallaxTargY-this.parallaxOffsetY)/10}var c=d.offsetWidth,e=d.offsetHeight;switch(g.directionX){case"left":this.position.x+this.speed.x+this.parallaxOffsetX<0&&(this.position.x=c-this.parallaxOffsetX);break;case"right":this.position.x+this.speed.x+this.parallaxOffsetX>c&&(this.position.x=0-this.parallaxOffsetX);break;default:(this.position.x+this.speed.x+this.parallaxOffsetX>c||this.position.x+this.speed.x+this.parallaxOffsetX<0)&&(this.speed.x=-this.speed.x)}switch(g.directionY){case"up":this.position.y+this.speed.y+this.parallaxOffsetY<0&&(this.position.y=e-this.parallaxOffsetY);break;case"down":this.position.y+this.speed.y+this.parallaxOffsetY>e&&(this.position.y=0-this.parallaxOffsetY);break;default:(this.position.y+this.speed.y+this.parallaxOffsetY>e||this.position.y+this.speed.y+this.parallaxOffsetY<0)&&(this.speed.y=-this.speed.y)}this.position.x+=this.speed.x,this.position.y+=this.speed.y},n.prototype.setStackPos=function(a){this.stackPos=a},h(),{option:o,destroy:p,start:m,pause:l}}var e="particleground",f=a.jQuery;a[e]=function(a,b){return new d(a,b)},a[e].defaults={minSpeedX:.1,maxSpeedX:.7,minSpeedY:.1,maxSpeedY:.7,directionX:"center",directionY:"center",density:1e4,dotColor:"#666666",lineColor:"#666666",particleRadius:7,lineWidth:1,curvedLines:!1,proximity:100,parallax:!0,parallaxMultiplier:5,onInit:function(){},onDestroy:function(){}},f&&(f.fn[e]=function(a){if("string"==typeof arguments[0]){var b,c=arguments[0],g=Array.prototype.slice.call(arguments,1);return this.each(function(){f.data(this,"plugin_"+e)&&"function"==typeof f.data(this,"plugin_"+e)[c]&&(b=f.data(this,"plugin_"+e)[c].apply(this,g))}),void 0!==b?b:this}return"object"!=typeof a&&a?void 0:this.each(function(){f.data(this,"plugin_"+e)||f.data(this,"plugin_"+e,new d(this,a))})})}(window,document),/**
     * requestAnimationFrame polyfill by Erik Möller. fixes from Paul Irish and Tino Zijdel
     * @see: http://paulirish.com/2011/requestanimationframe-for-smart-animating/
     * @see: http://my.opera.com/emoller/blog/2011/12/20/requestanimationframe-for-smart-er-animating
     * @license: MIT license
     */
    function(){for(var a=0,b=["ms","moz","webkit","o"],c=0;c<b.length&&!window.requestAnimationFrame;++c)window.requestAnimationFrame=window[b[c]+"RequestAnimationFrame"],window.cancelAnimationFrame=window[b[c]+"CancelAnimationFrame"]||window[b[c]+"CancelRequestAnimationFrame"];window.requestAnimationFrame||(window.requestAnimationFrame=function(b){var c=(new Date).getTime(),d=Math.max(0,16-(c-a)),e=window.setTimeout(function(){b(c+d)},d);return a=c+d,e}),window.cancelAnimationFrame||(window.cancelAnimationFrame=function(a){clearTimeout(a)})}();


    particleground(document.getElementById('particles-foreground'), {
        dotColor: 'rgba(255, 255, 255, 1)',
        lineColor: 'rgba(255, 255, 255, 0.05)',
        minSpeedX: 0.3,
        maxSpeedX: 0.6,
        minSpeedY: 0.3,
        maxSpeedY: 0.6,
        density: 50000, // One particle every n pixels
        curvedLines: false,
        proximity: 250, // How close two dots need to be before they join
        parallaxMultiplier: 10, // Lower the number is more extreme parallax
        particleRadius: 4, // Dot size
    });

    particleground(document.getElementById('particles-background'), {
        dotColor: 'rgba(255, 255, 255, 0.5)',
        lineColor: 'rgba(255, 255, 255, 0.05)',
        minSpeedX: 0.075,
        maxSpeedX: 0.15,
        minSpeedY: 0.075,
        maxSpeedY: 0.15,
        density: 30000, // One particle every n pixels
        curvedLines: false,
        proximity: 20, // How close two dots need to be before they join
        parallaxMultiplier: 20, // Lower the number is more extreme parallax
        particleRadius: 2, // Dot size
    });

</script>
#}
{{ theme_body() }}
{% block afterbody %}{% endblock %}
</body>
</html>
