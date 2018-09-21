function renderAry(elem, res, callback, mode){

  let fieldset = document.createElement('fieldset');
  let ul = document.createElement('ul');
  let _resultAry = {};
  let returnElem = '';

  ul.setAttribute('type', 'disc');
  fieldset.appendChild(ul);

   _resultAry = JSON.parse(res);

  returnElem += '<fieldset><legend id="searchWord"></legend><ul type=disc>';
  for(let key of _resultAry['match']){
    returnElem += '<li>' + key;
  }

  elem.innerHTML = returnElem + '</ul></fieldset>';

  callback(mode);
}

let render_callback = (mode) => {
  let searchWord = document.querySelector('#searchWord');

  let searchResultToDisplay = mode === 'dom1' ? dom_search_result[1]
                            : mode === 'dom2' ? dom_target_elem.value : '';

  searchWord.innerHTML = searchResultToDisplay + 'の検索結果';
}
