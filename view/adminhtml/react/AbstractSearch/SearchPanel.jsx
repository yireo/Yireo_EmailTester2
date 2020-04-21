import React, {useState, useEffect} from "react";
import DataTable from "./DataTable";

const SearchPanel = (props) => {
    const [search, setSearch] = useState("");
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(false);
    const [items, setItems] = useState([]);

    const searchViaAjax = (url, search) => {
        setLoading(true);
        const searchUrl = url + "?search=" + search;
        fetch(searchUrl)
            .then((response) => {
                if (!response.ok) {
                    throw Error(response.statusText);
                }
                setLoading(false);
                return response;
            })
            .then((response) => response.json())
            .then((responseJson) => setItems(responseJson))
            .catch((error) => setError(error));
    };

    useEffect(() => {
        searchViaAjax(props.ajaxUrl, search);
    }, [props.ajaxUrl, search]);

    const onRowClick = (itemId) => {
        props.setItemId(itemId);
        props.onClose();
    };

    return (
        <div className="admin__data-grid-outer-wrap">
            <div className="admin__data-grid-header">
                <div className="admin__data-grid-header-row">
                    <div className="data-grid-search-control-wrap">
                        <input
                            className="admin__control-text data-grid-search-control"
                            placeholder="Search"
                            value={search}
                            onChange={(event) => {
                                setSearch(event.target.value);
                                searchViaAjax(props.ajaxUrl, event.target.value);
                            }}
                        />
                    </div>
                </div>
            </div>
            <div className="admin__field-complex admin__field-complex-attributes">
                <div className="admin__data-grid-wrap">
                    {error && <div>Error: {error}</div>}
                    {!error && (
                        <DataTable
                            items={items}
                            fields={props.fields}
                            onRowClick={onRowClick}
                        />
                    )}
                </div>
            </div>
        </div>
    );
};

export default SearchPanel;
