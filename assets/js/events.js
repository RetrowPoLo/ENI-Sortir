//affichage cards ou table sur la page d'affichage des sorties
let tableViewType = document.getElementById("tableViewType")
let cardViewType = document.getElementById("cardViewType")

let viewTypeStored = localStorage.getItem('isTableView');
if(viewTypeStored !== null){
    let isTableView = JSON.parse(viewTypeStored)
    tableViewType.hidden = !isTableView;
    cardViewType.hidden = isTableView;
}
else{
    cardViewType.hidden = true
    localStorage.setItem('isTableView', JSON.stringify(true));
}

document.getElementById("changeDisplayType").addEventListener(
    "click",
    () => {
        tableViewType.hidden = !tableViewType.hidden;
        cardViewType.hidden = !cardViewType.hidden;
        viewTypeStored = localStorage.getItem('isTableView');

        if (viewTypeStored !== null) {
            let isTableView = JSON.parse(viewTypeStored);
            localStorage.setItem('isTableView', JSON.stringify(!isTableView));
        }
    },
    false,
);