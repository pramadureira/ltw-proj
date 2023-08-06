async function addTagNewTicket(event){
    const selectedOption = event.target.parentElement.querySelector('input')
    const submitOptions = event.target.parentElement.querySelector('input[name="tags"]')
    const options = event.target.parentElement.querySelectorAll('.create_ticket datalist option')

    const ul = document.querySelector('#tags > ul');

    options.forEach((option) => {
        if (option.textContent == selectedOption.value){
            const newLi = document.createElement('li')
            newLi.textContent = selectedOption.value
            newLi.classList.add('tag')
            ul.appendChild(newLi)

            let currentTags = Array.from(ul.childNodes).map(function (tag) {
                return tag.textContent
            })
            
            submitOptions.value = JSON.stringify(currentTags)
        }
    })

    selectedOption.value = ""
}

function new_ticket_tags() {
    const selectTags = document.querySelector('.create_ticket #tags > input');
    const addTags = document.querySelector('.create_ticket #tags > img');

    if (selectTags && addTags){

        document.body.addEventListener('click', async function (event) {
            if (event.target.tagName === 'LI') {
                const selectedOption = event.target.innerHTML
                const submitOptions = event.target.parentElement.parentElement.querySelector('input[name="tags"]')

                const ul = document.querySelector('.create_ticket #tags > ul');
                let currentTags = []

                for (let i = 0; i < ul.childNodes.length; ){
                    if (ul.childNodes[i].textContent == selectedOption) {
                        ul.removeChild(ul.childNodes[i])
                    } else {
                        currentTags.push(ul.childNodes[i].textContent)
                        i++
                    }
                }
                
                if (currentTags.length == 0) submitOptions.value = "[]"
                else submitOptions.value = JSON.stringify(currentTags)
            }
        });

        addTags.addEventListener('click', (event) => {
            addTagNewTicket(event)
        })

        selectTags.addEventListener('keydown', (event) => {
            if (event.key === 'Enter'){
            event.preventDefault()
            addTagNewTicket(event)
            }
        })
    }
}

new_ticket_tags()