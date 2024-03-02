{% extends 'layout/layout.twig.php'  %}

{% block title %}Página não encontrada - Produtos{% endblock %}

{% block content %}
<div class="title">
    <h2 class="section--title">{{title}}</h2>
</div>
<div>
    {{message}}
</div>
{% endblock %}