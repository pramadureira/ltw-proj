const activeTicketsBtn = document.getElementById('activeTicketsBtn')
const ticketStatusBtn = document.getElementById('ticketStatusBtn')
const userStatsBtn = document.getElementById('userStatsBtn')

if (activeTicketsBtn != null) activeTicketsBtn.addEventListener('click', showActiveTicketsChart);
if (ticketStatusBtn != null) ticketStatusBtn.addEventListener('click', showTicketStatusChart);
if (userStatsBtn != null) userStatsBtn.addEventListener('click', showUserStatsChart);


async function showActiveTicketsChart() {
    const response = await fetch('../api/ticket.php/', {
        method: 'get',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
    })

    const data = await response.json()
    const activeTickets = []
    Object.entries(data.activeTicketsData).forEach(([date, count]) => {
        activeTickets.push({ date, count })
    })

    renderChart(activeTickets, 'line')
    
}
  
async function showTicketStatusChart() {
    const response = await fetch('../api/ticket.php/', {
        method: 'get',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
    })

    const data = await response.json()
    const ticketStatus = []

    Object.entries(data.ticketStatusData).forEach(([label, count]) => {
        ticketStatus.push({ label, count })
    })

    renderChart(ticketStatus, 'doughnut')
}
  
async function showUserStatsChart() {
    const response = await fetch('../api/client.php/', {
        method: 'get',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
    })

    const data = await response.json()
    const userStats = []

    Object.entries(data).forEach(([label, count]) => {
        userStats.push({ label, count })
    })

    renderChart(userStats, 'doughnut')
}


function renderChart(data, type) {
    document.getElementById('chartContainer').innerHTML = ''
  
    const canvas = document.createElement('canvas')
    canvas.id = 'chart'
    document.getElementById('chartContainer').appendChild(canvas)
    
    const ctx = document.getElementById('chart').getContext('2d');

    const chartConfig = {
        type: type,
        data: {
            labels: (type === 'line') ? data.map(entry => entry.date).reverse() : data.map(item => item.label || item.date),
            datasets: [{
                label: (type === 'line') ? 'Open Tickets' : undefined,
                data: (type === 'line') ? data.map(item => item.count).reverse() : data.map(item => item.count),
                backgroundColor: (type === 'line') ? '#ff6384' : ['#ff6384', '#36a2eb', '#ffce56'],
                borderWidth: 1,
                borderColor: (type === 'line') ? '#ff6384' : '#fff',
                showLine: true
            }]
        },
        options: {
            responsive: true,
        }
    };

    new Chart(ctx, chartConfig)
}
