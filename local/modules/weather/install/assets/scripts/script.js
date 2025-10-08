$(function() {

    let params = $('#weather-params').data().params;

    console.log(params.icons);

    let html = '<div class="b24-app-block b24-app-desktop">';
    html += '<div class="b24-app-block-header" style="background:' + params.color + ';">ПОГОДА';
    html += '<div>Страна: ' + params.country + '</div>';
    html += '<div>Город: ' + params.province + '</div>';
    html += '<div class="b24-app-block-content">';

    let options = new Map([
        ['Температура (г): ', params.temp],
        ['Чувствуется как (г): ', params.feels_like],
        ['Скорость ветра (м/с): ', params.wind_speed],
        ['Направление ветра: ', params.wind_dir],
        ['Давление (мм рт ст): ', params.pressure_mm],
        ['Влажность (%): ', params.humidity]
    ]);

    let icons = ["macos", "windows", "linux", "macos", "windows", "linux"];
    
    let i = 0;
    for (let pair of options.entries()) {
        html += '<div class="sidebar-widget-item --with-separator weather-item">'; 
        html += '<div class="task-item weather-task">';
        if (params.icons == 'Y') {
            html += '<span class="task-item-counter-wrap"><span class="task-item-counter b24-app-icon b24-app-icon-' + icons[i] + '"></span></span>';
        }
        html += '<span class="task-item-text weather-text" style="color:' + params.text_color + ';">' + pair[0] + '</span>';
        html += '<span class="task-item-index weather-text"> ' + pair[1]; 
        html += '</span></div></div>';
        i++;
    }
    
    html += '<div style="clear:both"></div>';
    html += '</div></div>';

    $('#pulse_open_btn').after(html);
});
