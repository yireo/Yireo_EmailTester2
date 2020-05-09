import React, {useState, useEffect} from "react";
import SearchPanel from "./AbstractSearch/SearchPanel";
import ModalOverlay from "./AbstractSearch/ModalOverlay";

const AbstractSearch = (props) => {
    const [itemId, setItemId] = useState(props.id);
    const [itemLabel, setItemLabel] = useState(props.emptyLabel);
    const [showSearchPanel, setShowSearchPanel] = useState(false);

    const setLabelViaAjax = (id) => {
        if (!id > 0) return false;

        const searchUrl = props.labelAjaxUrl + "?id=" + id;
        fetch(searchUrl)
            .then((response) => {
                if (!response.ok) {
                    throw Error(response.statusText);
                }
                return response;
            })
            .then((response) => response.json())
            .then((responseJson) => setItemLabel(responseJson.label));

        return true;
    };

    const changeItemData = (id) => {
        setLabelViaAjax(id);
        setItemId(id);
    };

    useEffect(() => {
        setLabelViaAjax(props.id);
    }, [props.id]);

    return (
        <div>
            <div className="id-preview-container">
                <div className="admin__control-addon id-preview">
                    <input className="admin__control-text" type="text" name="product_id" placeholder="Numeric ID"
                           onChange={(event) => changeItemData(event.target.value)}
                           value={itemId} maxLength="11"/>
                    <label className="admin__addon-suffix">
                        <span>{itemLabel}</span>
                    </label>
                </div>

                <div className="button">
                    <small>
                        <button onClick={() => setShowSearchPanel(true)}>Search</button>
                    </small>
                </div>
            </div>

            {showSearchPanel && <div>
                <ModalOverlay
                    panelTitle={props.panelTitle}
                    onClose={() => setShowSearchPanel(false)}
                >
                    <SearchPanel
                        setItemId={changeItemData}
                        ajaxUrl={props.searchAjaxUrl}
                        fields={props.fields}
                        onClose={() => setShowSearchPanel(false)}
                    />
                </ModalOverlay>
            </div>}
        </div>
    )
};

export default AbstractSearch;
