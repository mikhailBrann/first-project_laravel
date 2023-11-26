require('./bootstrap');

class FormTodoList {
    constructor(inputNode, parentNode, listNode) {
        this.inputNode = inputNode;
        this.parentNode = parentNode;
        this.listNode = listNode;

        this.renderList();
    }

    changeDataProp(listObj, value='', updateFlag=false) {
        if(value != '' || updateFlag) {
            let list = listObj.listNode.querySelectorAll('.js__todo-item');
            let newData = {};

            if(list.length) {
                list.forEach(elem => {
                    let key = elem.querySelector('[data-type=key]');
                    let value = elem.querySelector('[data-type=value]');

                    newData[key.value] = value.value;
                });
            }

            listObj.inputNode.value = JSON.stringify(newData);
            listObj.renderList();
        }
    }

    renderItemList(key='', value='') {
        let itemNode = document.createElement('div');
        let itemNodeTop = document.createElement('div');
        let itemNodeBottom = document.createElement('div');

        let keyNode = document.createElement('input');
        let valueNode = document.createElement('input');
        let removeBtn = document.createElement('span');
        let titleHead = document.createElement('span');
        let valueHead = document.createElement('span');
        let listObj = this;

        itemNode.classList.add('js__todo-item');
        itemNodeTop.classList.add('js__todo-item-top');
        itemNodeBottom.classList.add('js__todo-item-bottom');

        keyNode.classList.add('js__todo-key', 'form__input');
        valueNode.classList.add('js__todo-val', 'form__input');
        removeBtn.classList.add('js__todo-del');

        keyNode.setAttribute('type', 'text');
        keyNode.setAttribute('value', key);
        keyNode.dataset.type = 'key';

        valueNode.setAttribute('type', 'text');
        valueNode.setAttribute('value', value);
        valueNode.dataset.type = 'value';

        titleHead.innerText = 'Название';
        valueHead.innerText = 'Значение';

        keyNode.addEventListener("change", (event) => {
            listObj.changeDataProp(listObj, event.currentTarget.value);
        });

        valueNode.addEventListener("change", (event) => {
            listObj.changeDataProp(listObj, event.currentTarget.value);
        });

        removeBtn.addEventListener("click", (event) => {
            let confirmAgree = confirm(`Вы точно хотите удалить этот аттрибут?`);

            if(confirmAgree) {
                listObj.listNode.removeChild(itemNode);
                listObj.changeDataProp(listObj, '', true);
            }
        });

        itemNodeTop.append(titleHead, valueHead);
        itemNodeBottom.append(keyNode, valueNode, removeBtn)
        itemNode.append(itemNodeTop, itemNodeBottom);

        return itemNode;
    }

    renderList() {
        if(this.inputNode.value != '') {
            let dataParse = JSON.parse(this.inputNode.value);
            this.listNode.innerHTML = '';

            for(let key in dataParse) {
                let item = this.renderItemList(key, dataParse[key]);
                this.listNode.append(item);
            }
        }
    }
}

/*функция отвечает за кастомизацию выпадающего списка для поля в форме изменения товара*/
function dropdownChange(currentElem, parentSelector, changeSelector, dataSelector) {
    let parentNode = currentElem.closest(parentSelector);
    let viewChangeNode = parentNode.querySelector(changeSelector);
    let dataNode = parentNode.querySelector(dataSelector);

    if(viewChangeNode && dataNode) {
        dataNode.setAttribute("value", currentElem.dataset.value);
        viewChangeNode.innerText = currentElem.dataset.name;
        parentNode.classList.remove('active');
    }
}

window.onload = () => {
    let formDropdownCheckers = document.querySelectorAll('.form-dropdown__checker');
    if(formDropdownCheckers.length) {
        formDropdownCheckers.forEach(elem => {
            elem.addEventListener("click", (event) => {
                let parentNode = event.currentTarget.closest(".form-dropdown");
                parentNode.classList.toggle('active')
            });
        });
    }

    let dropdownFormChangeFields = document.querySelectorAll('.form-dropdown__item[data-type-prop=status]');
    if(dropdownFormChangeFields.length) {
        dropdownFormChangeFields.forEach(elem => {
            elem.addEventListener("click", (event) => dropdownChange(
                event.currentTarget,
                '.form-dropdown',
                '.form-dropdown__checker',
                '[name=status]',
            ));
        });
    }

    let todolistFormField = document.querySelectorAll('.form__input[name=data]');
    if(todolistFormField.length) {
        todolistFormField.forEach(elem => {
            let parentNode = elem.closest('.form-todolist');
            let listNode = parentNode.querySelector('.form-todolist__list')
            let addedItemBtn = parentNode.querySelector('.form-todolist__added-btn');

            if(parentNode && listNode && addedItemBtn) {
                let propTodoList = new FormTodoList(elem , parentNode, listNode);

                addedItemBtn.addEventListener("click", event => {
                    let newTodoItem = propTodoList.renderItemList();
                    propTodoList.listNode.append(newTodoItem);
                });
            }
        });
    }
}
