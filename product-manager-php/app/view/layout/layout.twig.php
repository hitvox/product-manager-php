<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/modal.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/waitme@1.19.0/waitMe.min.css" integrity="sha256-f4pKuDVe4fH+x/e/ZkA4CgDKOA5SuSlvCnB4BjMb4Ys=" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <title>{% block title %}{% endblock %} - Produtos</title>
</head>
<body>
    <section class="header">
        <div class="logo">
            <i class="ri-menu-line icon menu"></i>
            <h2>Produtos</h2>
        </div>
    </section>
    <section class="main">
        <div class="sidebar">
            <ul class="sidebar--items">
                <li>
                    <a href="/" {{ (currentUrl == '/') ? 'class="active"' : '' }}>
                        <span class="icon"><i class="ri-home-5-line"></i></span>
                        <span class="sidebar--item">Início</span>
                    </a>
                </li>
                <li>
                    <a href="/categories" {{ (currentUrl == '/categories') ? 'class="active"' : '' }}>
                        <span class="icon"><i class="ri-list-check"></i></span>
                        <span class="sidebar--item">Categorias</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="main--content">
            {% block content %}{% endblock %}
        </div>
    </section>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/waitme@1.19.0/waitMe.min.js" integrity="sha256-oGX4TEGGqGIQgVjZLz74NPm62KtrhR94cxSTRpzcN+o=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-maskmoney/3.0.2/jquery.maskMoney.min.js" integrity="sha512-Rdk63VC+1UYzGSgd3u2iadi0joUrcwX0IWp2rTh6KXFoAmgOjRS99Vynz1lJPT8dLjvo6JZOqpAHJyfCEZ5KoA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="/assets/js/main.js"></script>
    <script src="/assets/js/modal.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {% block scripts %}{% endblock %}
</body>
</html>