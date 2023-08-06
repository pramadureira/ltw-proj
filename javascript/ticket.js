function encodeForAjax(data) {
  return Object.keys(data).map(function(k){
    return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
  }).join('&')
}

async function addTagTicket(event){
  const ticketId = event.target.parentElement.parentElement.parentElement.getAttribute('data-id')
  const selectedOption = event.target.parentElement.querySelector('input')
  
  const response = await fetch('../api/ticket.php/', {
    method: 'put',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: encodeForAjax({field: 'hashtag', ticketId: ticketId, hashtag: selectedOption.value})
  })

  const ul = document.querySelector('#ticket #tags > ul');

  if (response.status === 200) {
    const newLi = document.createElement('li');
    newLi.textContent = selectedOption.value;
    newLi.classList.add('tag')
    ul.appendChild(newLi);

    ticket_update_changes()
  }

  selectedOption.value = ""
}

async function ticket_remove_tag(event) {
  if (event.target.tagName === 'LI') {
    const ticketId = event.target.parentElement.parentElement.parentElement.parentElement.getAttribute('data-id')
    const selectedOption = event.target.innerHTML

    const ul = document.querySelector('#ticket #tags > ul');
    
    const response = await fetch('../api/ticket.php/', {
      method: 'delete',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: encodeForAjax({field: 'hashtag', ticketId: ticketId, hashtag: selectedOption})
    })

    if (response.status === 200) {
      ul.removeChild(event.target)
      ticket_update_changes()
    }
  }
}

function ticket_tags() {
  const selectTags = document.querySelector('#ticket #tags > input');
  const addTags = document.querySelector('#ticket #tags > img');

  if (selectTags && addTags){

    document.body.addEventListener('click', ticket_remove_tag);

    addTags.addEventListener('click', (event) => {
      addTagTicket(event)
    })

    selectTags.addEventListener('keydown', (event) => {
      if (event.key === 'Enter'){
        addTagTicket(event)
      }
    })
  }
}

function ticket_department() {
  const selectDepartment = document.querySelector('#ticket #department > select')

  if (selectDepartment) {
    selectDepartment.addEventListener('change', async function (event) {
      const ticketId = event.target.parentElement.parentElement.parentElement.getAttribute('data-id')

      const response = await fetch('../api/ticket.php/', {
        method: 'put',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: encodeForAjax({field: 'department', ticketId: ticketId, department: selectDepartment.value})
      })

      if (response.status === 200) {
        //when department changes, need to change the available agents
        const assignAgent = document.querySelector('#ticket #agent > select')
        assignAgent.innerHTML = ''

        const hintText = document.createElement('option')
        hintText.textContent = 'assign an agent'
        hintText.disabled = true
        hintText.selected = true
        
        assignAgent.appendChild(hintText)

        const response = await fetch('../api/get_department_agents.php?department=' + selectDepartment.value);
        const department_agents = await response.json()

        department_agents.forEach((element) => {
          const agent = document.createElement('option')
          agent.textContent = element['username']
          assignAgent.appendChild(agent)
        })

        ticket_update_status(ticketId, 'Open');
        ticket_update_changes()
      }
    })
  }
}

function ticket_close() {
  document.querySelector('#ticket #close_ticket').innerHTML = ''
  ticket_update_status(document.querySelector('#ticket').getAttribute('data-id'), 'Closed')

  const selectDepartment = document.querySelector('#ticket #department > select')
  if (selectDepartment) {
    selectDepartment.querySelectorAll('option').forEach((option) => {
      if (!option.selected)
        selectDepartment.removeChild(option)
    })
  }

  const selectAgent = document.querySelector('#ticket #agent > select')
  if (selectAgent) {
    selectAgent.querySelectorAll('option').forEach((option) => {
      if (!option.selected)
        selectAgent.removeChild(option)
    })
  }

  const selectStatus = document.querySelector('#ticket #status > select')
  if (selectStatus) {
    selectStatus.querySelectorAll('option').forEach((option) => {
      if (!option.selected)
        selectStatus.removeChild(option)
    })
  }

  const tags = document.querySelector('#ticket #tags')
  const tag_input = tags.querySelector('input')
  const tag_add = tags.querySelector('img')

  tags.removeChild(tag_input)
  tags.removeChild(tag_add)

  document.body.removeEventListener('click', ticket_remove_tag)

  const separator = document.querySelector('#ticket #comments div')
  separator.outerHTML = ''

  const comment_form = document.querySelector('#ticket #comments form')
  comment_form.outerHTML = ''

  ticket_update_changes()
}

let initialValue = null

async function ticket_update_status(ticketId, new_status){
  const response = await fetch('../api/ticket.php/', {
    method: 'put',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: encodeForAjax({field: 'status', ticketId: ticketId, oldStatus: initialValue, newStatus: new_status})
  })

  if (response.status === 200) {
    initialValue = new_status

    const status = document.querySelector('#ticket #status select')
    status.childNodes.forEach((option) => {
      if (option.value === new_status)
        option.selected = true
      else option.selected = false
    })
  }
}

function ticket_status() {
  const status = document.querySelector('#ticket #status select')

  if (status !== null) {
    initialValue = status.value

    status.addEventListener('change', function(event){
      const ticketId = event.target.parentElement.parentElement.parentElement.getAttribute('data-id')
      const new_status = event.target.value

      if (new_status === initialValue) return

      const agent = document.querySelector('#ticket #agent select').value

      if (new_status === 'Open' && agent !== 'assign an agent') {
        event.target.value = initialValue
      } else if (new_status === 'Assigned' && agent === 'assign an agent'){
        event.target.value = initialValue
      } else {
        if (new_status === 'Closed') {
          ticket_close()
        } else {
          ticket_update_status(ticketId, new_status)
          ticket_update_changes()
        }
        initialValue = new_status
      }
    })
  }
}

function ticket_agent(){
  const selectAgent = document.querySelector('#ticket #agent > select')
  if (selectAgent) {
    selectAgent.addEventListener('change', async function(event) {
      const ticketId = event.target.parentElement.parentElement.parentElement.getAttribute('data-id')

      const response = await fetch('../api/ticket.php/', {
        method: 'put',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: encodeForAjax({field: 'agent', ticketId: ticketId, agent: selectAgent.value})
      })

      if (response.status === 200) {
        ticket_update_status(ticketId, 'Assigned');
        ticket_update_changes()
      }
    })
  }
}

function manage_dropdown(e){
  const menuContent = e.target.nextElementSibling;
  if (!menuContent.classList.contains("show")) {
    menuContent.classList.add("show");
    menuContent.classList.remove("hide");
  } else {
    menuContent.classList.add("hide");
    menuContent.classList.remove("show");
  }
}

function ticket_changes() {
  const dropdownButton = document.querySelector('#toggle_show_changes');

  if (dropdownButton) {
    dropdownButton.addEventListener('click', manage_dropdown);
  }
}

async function ticket_update_changes() {
  const changes_menu = document.querySelector('#ticket #changes_menu')

  if (changes_menu) {
    const ticketId = document.querySelector('#ticket').getAttribute('data-id')

    const response = await fetch('../api/get_ticket_changes.php?ticketId=' + ticketId)
    const changes = await response.json()

    let ol = changes_menu.querySelector('#ticket_changes ol')

    if (ol) {
      while (ol.firstChild){
        ol.removeChild(ol.firstChild)
      }
    } else {
      const changes_menu = document.querySelector('#changes_menu')

      const toggle_show_changes = document.createElement('div')
      toggle_show_changes.id = 'toggle_show_changes'
      toggle_show_changes.innerHTML = '<img src="../images/icons/history.png" alt="changes"> Show all changes (<span id="change_count"></span>)'

      const ticket_changes = document.createElement('div')
      ticket_changes.id = 'ticket_changes'
      ol = document.createElement('ol')
      ticket_changes.appendChild(ol)

      changes_menu.appendChild(toggle_show_changes)
      changes_menu.appendChild(ticket_changes)

      toggle_show_changes.addEventListener('click', manage_dropdown)
    }

    let change_count = 0

    changes.forEach((change) => {
      const li = document.createElement('li')
      const time = document.createElement('time')
      time.dateTime = change['date']
      time.textContent = change['date']

      li.appendChild(time)

      li.innerHTML += ` <strong>${change["userId"]}</strong>`

      if (change['field'] === 'Hashtag') {
        if (change['old'] === '') {
          li.innerHTML += ` added the tag <strong>${change['new']}</strong>`
        } else if (change['new'] === '') {
          li.innerHTML += ` removed the tag <strong>${change['old']}</strong>`
        }
      } else if (change['field'] === 'Agent') {
        if (change['old'] === '') {
          li.innerHTML += ` assigned the ticket to <strong>${change['new']}</strong>`
        } else {
          li.innerHTML += ` reassigned the ticket from <strong>${change['old']}</strong>
                            to <strong>${change['new']}</strong>`
        }
      } else if (change['field'] === 'Department') {
        if (change['old'] === '') {
          li.innerHTML += ` changed the department to <strong>${change['new']}</strong>`
        } else {
          li.innerHTML += ` changed the department from <strong>${change['old']}</strong>
                            to <strong>${change['new']}</strong>`
        }
      } else if (change['field'] === 'Status') {
        li.innerHTML += ` changed the ticket from <strong>${change['old']}</strong>
                          to <strong>${change['new']}</strong>`
      }

      ol.appendChild(li)
      change_count++
    })

    const toggle = changes_menu.querySelector('#toggle_show_changes #change_count')
    toggle.textContent = change_count
  }
}

function ticket() {
  ticket_tags()
  ticket_department()
  ticket_agent()
  ticket_changes()
  ticket_status()

  const closeButton = document.querySelector('#ticket #close_ticket')

  if (closeButton) {
    closeButton.addEventListener('click', ticket_close)
  }
}

ticket()
