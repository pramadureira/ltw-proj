function encodeForAjax(data) {
  return Object.keys(data).map(function(k){
    return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
  }).join('&')
}


const inpFileReg = document.querySelector("section#register > form > label > input")

if (inpFileReg !== null) {
inpFileReg.addEventListener("change", function() {
  const profPicPreviewReg = document.querySelector("section#register img")
  const file = this.files[0]
  if (file) {
    const reader = new FileReader()
    reader.addEventListener("load", function() {
        profPicPreviewReg.setAttribute("src", this.result)
        profPicPreviewReg.style.filter = 'none';
        profPicPreviewReg.style.borderRadius = '50%';
    });

    reader.readAsDataURL(file)
  }
})}



const selects = document.querySelectorAll('section#form-manage-users td:nth-child(2) > select');
const selectsDepartments = document.querySelectorAll('section#form-manage-users td:nth-child(3) > .departments > select');

if (selects.length !== 0 && selectsDepartments.length !== 0) {
  document.body.addEventListener('change', async function (event) {
    if (event.target.tagName === "SELECT" && event.target.value == "client") {
      const id = event.target.parentElement.parentElement.getAttribute('data-id')
      const ul = document.querySelector('section#form-manage-users tr[data-id="' + id + '"] td:nth-child(3) > .departments > ul');
      while (ul.firstChild) {
        ul.removeChild(ul.firstChild);
      }
    }
  });
  document.body.addEventListener('click', async function (event) {
    if (event.target.tagName === 'LI') {
      const id = event.target.parentElement.parentElement.parentElement.parentElement.getAttribute('data-id')

      if (id == null) return;
      const selectedOption = event.target.innerHTML
      
      const ul = document.querySelector('section#form-manage-users tr[data-id="' + id + '"] td:nth-child(3) > .departments > ul');
      
      const response = await fetch('../api/client.php/', {
        method: 'delete',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: encodeForAjax({field: 'department', username: id, value: selectedOption})
      })
      const client = await response.json()

      if (client !== null) {
        while (ul.firstChild) {
          ul.removeChild(ul.firstChild);
        }

        const departments = client['departments']
        departments.forEach((element) => {
          const newLi = document.createElement('li');
          newLi.textContent = element.name;
          ul.appendChild(newLi);
        });
      }
    }
  });

  selects.forEach(select => {
    select.addEventListener('change', async (event) => {
      const selectedOption = event.target.value
      const id = event.target.parentElement.parentElement.getAttribute('data-id')
      const isAdmin = selectedOption == "admin" ? 1 : 0
      const isAgent = isAdmin || selectedOption == "agent" ? 1 : 0
      const response = await fetch('../api/client.php/', {
        method: 'put',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: encodeForAjax({field: 'role', username: id, isAgent: isAgent, isAdmin: isAdmin})
      })

      const client = await response.json()
      if (client !== null) {
        const select = document.querySelector('section#form-manage-users tr[data-id="' + client['username'] + '"] > td:nth-child(2) > select')
        if (client['isAdmin']) {
          select.value = 'admin'
        } else if (client['isAgent']) {
          select.value = 'agent'
        } else {
          select.value = 'client'
        }
      }
    });
  });

  selectsDepartments.forEach(select => {
    select.addEventListener('change', async (event) => {
      const id = event.target.parentElement.parentElement.parentElement.getAttribute('data-id')
      const selectedOption = event.target.value
      select.value = "unspecified"
          
      const response = await fetch('../api/client.php/', {
        method: 'post',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: encodeForAjax({field: 'department', username: id, value: selectedOption})
      })
      const client = await response.json()

      if (client !== null) {
        const ul = document.querySelector('section#form-manage-users tr[data-id="' + id + '"] td:nth-child(3) > .departments > ul');

        while (ul.firstChild) {
          ul.removeChild(ul.firstChild);
        }

        const departments = client['departments']
        departments.forEach((element) => {
          const newLi = document.createElement('li');
          newLi.textContent = element.name;
          ul.appendChild(newLi);
        });
      }
    });
  });
}


const addDepButton = document.querySelector("div#add-department img")

if (addDepButton !== null) {
  addDepButton.addEventListener('click', async function (event) {
    const newDepInput = document.querySelector("div#add-department input")

    const response = await fetch('../api/new_entity.php/', {
      method: 'post',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: encodeForAjax({type: 'department', value: newDepInput.value})
    })

    if (response.status === 200) location.reload()
    
    newDepInput.value=""
});}

const addStatusButton = document.querySelector("div#add-status img")

if (addStatusButton !== null) {
  addStatusButton.addEventListener('click', async function (event) {
    const newStatusInput = document.querySelector("div#add-status input")

    const response = await fetch('../api/new_entity.php/', {
      method: 'post',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: encodeForAjax({type: 'status', value: newStatusInput.value})
    })
    
    if (response.status === 200) location.reload()
    
    newStatusInput.value=""
  });
}


const addHashtagButton = document.querySelector("div#add-htag img")

if (addHashtagButton !== null) {
  addHashtagButton.addEventListener('click', async function (event) {
    const newHashtagInput = document.querySelector("div#add-htag input")

    const response = await fetch('../api/new_entity.php/', {
      method: 'post',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: encodeForAjax({type: 'hashtag', value: newHashtagInput.value})
    })
    
    if (response.status === 200) location.reload()
    
    newHashtagInput.value=""
  });
}

const addFAQButton = document.querySelector("div#add-htag img")

if (addHashtagButton !== null) {
  addHashtagButton.addEventListener('click', async function (event) {
    const newHashtagInput = document.querySelector("div#add-htag input")

    const response = await fetch('../api/new_entity.php/', {
      method: 'post',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: encodeForAjax({type: 'faq', value: newHashtagInput.value})
    })

    if (response.status === 200) location.reload()

    newHashtagInput.value=""
  });
}