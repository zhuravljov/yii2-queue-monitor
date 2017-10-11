function renderClassesPie(id, data) {
    renderPie(id, data);
}

function renderSendersPie(id, data) {
    renderPie(id, data);
}

function renderPie(id, data) {
    var pie = new Chart(document.getElementById(id).getContext('2d'), {
        type: 'pie',
        data: {
            datasets: [{
                data: data.map(function(d) {
                    return parseInt(d.count);
                }),
                backgroundColor: [
                    '#537bc4',
                    '#acc236',
                    '#166a8f',
                    '#00a950',
                    '#58595b',
                    '#8549ba'
                ],
                label: 'Dataset 1'
            }],
            labels: data.map(function(d) {
                return d.name + ': ' + d.count;
            })
        },
        options: {
            responsive: true,
            legend: {
                position: 'right'
            },
            tooltips: {
                callbacks: {
                    label: function (item) {
                        return data[item.index].name + ': ' + data[item.index].count;
                    }
                }
            }
        }
    });
}
