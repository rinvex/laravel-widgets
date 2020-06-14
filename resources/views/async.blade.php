<script>
    var widgetTimer{{ $params['id'] }} = setInterval(function () {
        if (window.$) {
            $('#rinvex-widgets-container-{{ $params['id'] }}').load('{{ route('rinvex.widgets.async', http_build_query($params)) }}');
            clearInterval(widgetTimer{{ $params['id'] }});
        }
    }, 100);
</script>
