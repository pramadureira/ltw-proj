async function addTagSearch(event){
    const selectedOption = event.target.parentElement.querySelector('input')
    const options = event.target.parentElement.querySelectorAll('#tickets datalist option')

    const ul = document.querySelector('#tags > ul');

    options.forEach((option) => {
        if (option.textContent == selectedOption.value){
            const newLi = document.createElement('li')
            newLi.textContent = selectedOption.value
            newLi.classList.add('tag')
            ul.appendChild(newLi)
        }
    })

    selectedOption.value = ""
}

function search_tags() {
    const selectTags = document.querySelector('#tickets #tags > input');
    const addTags = document.querySelector('#tickets #tags > img');

    if (selectTags && addTags){

        document.body.addEventListener('click', async function (event) {
            if (event.target.tagName === 'LI') {
                const selectedOption = event.target.innerHTML

                const ul = document.querySelector('#tickets #tags > ul');
                
                ul.childNodes.forEach((li) => {
                    if (li.textContent == selectedOption)
                        ul.removeChild(li)
                })
                filter()
            }
        });

        addTags.addEventListener('click', (event) => {
            addTagSearch(event)
            filter()
        })

        selectTags.addEventListener('keydown', (event) => {
            if (event.key === 'Enter'){
                event.preventDefault()
                addTagSearch(event)
                filter()
            }
        })
    }
}

function search_date(){
    const startDate = document.querySelector('#tickets #date_filter #start_date')
    const endDate = document.querySelector('#tickets #date_filter #end_date')

    startDate.addEventListener('change', filter)
    endDate.addEventListener('change', filter)
}

function search_agent(){
    const agent = document.querySelector('#tickets #agent_filter')
    if (agent != null) {
        let timer

        agent.addEventListener('input', function () {
            clearTimeout(timer)

            timer = setTimeout(() => {
                filter()
            }, 500)
        })
    }
}

async function filter(){
    const input = document.querySelector('#tickets #searchticket').value
    const selectedTags = Array.from(document.querySelector('#tickets #tags > ul').childNodes).map((tag) => tag.textContent)

    let startDate = document.querySelector('#tickets #date_filter #start_date').value
    let endDate = document.querySelector('#tickets #date_filter #end_date').value

    const agentS = document.querySelector('#tickets #agent_filter input')
    const agent = agentS == null ? "" : agentS.value

    const department = document.querySelector('#tickets #department_filter select').value

    const status = document.querySelector('#tickets #status_filter select').value
    const priority = document.querySelector('#tickets #priority_filter select').value

    const response = await fetch('../api/search_tickets.php?search=' + input + '&agent=' + agent 
                                    + '&department=' + department + '&status=' + status + '&priority=' + priority)
    const tickets = await response.json()

    const section = document.querySelector('#previews')
    const previews = document.querySelectorAll('.ticketpreview')
    
    for (const preview of previews)
        preview.outerHTML = '';

    for (const ticket of tickets) {
        const ticket_hashtags = ticket['hashtags']
        
        let tags_match = true
                
        selectedTags.forEach((tag) => {
            if (!ticket_hashtags.includes(tag))
                tags_match = false
        })

        if (!tags_match) continue

        if (!startDate) startDate = '1970-01-01'
        if (!endDate) endDate = new Date().toISOString().slice(0,10)

        const ticketDate = ticket['date']['date'].slice(0,10)

        if (ticketDate < startDate || ticketDate > endDate) continue

        const link = document.createElement('a')
        link.classList.add('ticketpreview')
        link.href = '../pages/ticket.php?id=' + ticket.ticketId

        const title = document.createElement('h3')
        title.textContent = ticket.title

        const description = document.createElement('p')
        const encoder = new TextEncoder()                   //all of this because php counts bytes
        const bytes = encoder.encode(ticket.body)
        if (bytes.length > 200){
            const decoder = new TextDecoder()
            const str = decoder.decode(bytes.subarray(0, 200))
            description.textContent = str + '...'
        }
        else description.textContent = ticket.body

        const div_tags = document.createElement('div')
        div_tags.id = 'tags'
        const list_tags = document.createElement('ul')
        ticket_hashtags.forEach((tag) => {
            const tag_item = document.createElement('li')
            tag_item.classList.add('tag')
            tag_item.textContent = tag
            list_tags.appendChild(tag_item)
        })
        div_tags.appendChild(list_tags)

        const status = document.createElement('div')
        status.id = 'status'
        status.textContent = 'Status: ' + ticket.status

        const date = document.createElement('time')
        date.datetime = ticket.date
        date.textContent = 'Date: ' + ticket.date.date.substring(0, 10)

        link.appendChild(title)
        link.appendChild(description)
        link.appendChild(div_tags)
        link.appendChild(status)
        link.appendChild(date)

        section.appendChild(link)
    }
}

function search_filters(){
    const toggle = document.querySelector('#tickets img')

    if (toggle) {
        toggle.addEventListener('click', function() {
            const menuContent = document.querySelector('#search_filters')
            if (!menuContent.classList.contains("show")) {
              menuContent.classList.add("show");
              menuContent.classList.remove("hide");
            } else {
              menuContent.classList.add("hide");
              menuContent.classList.remove("show");
            }
          });
    }
}

function search_tickets(){
    const searchBox = document.querySelector('#searchticket')

    if (searchBox) {
        search_filters()
        search_tags()
        search_date()
        search_agent()
        searchBox.addEventListener('input', filter)

        const department = document.querySelector('#tickets #department_filter select')
        department.addEventListener('change', filter)

        const status = document.querySelector('#tickets #status_filter select')
        status.addEventListener('change', filter)

        const priority = document.querySelector('#tickets #priority_filter select')
        priority.addEventListener('change', filter)
    }
}

search_tickets()