import React, {useState, useEffect} from "react";
import DataTable from "./DataTable";

const SearchPanel = (props) => {
    const [search, setSearch] = useState("");
    const [sortField, setSortField] = useState("");
    const [sortDirection, setSortDirection] = useState("ASC");
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(false);
    const [items, setItems] = useState([]);

    const searchViaAjax = (url, search, sortField, sortDirection) => {
        setLoading(true);
        setError(false);
        const searchUrl = url + "?search=" + search + "&sortField=" + sortField + "&sortDirection=" + sortDirection;
        fetch(searchUrl)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(response.statusText);
                }
                setLoading(false);
                return response;
            })
            .then((response) => response.json())
            .then((responseJson) => {
                if (responseJson.length > 0) {
                    setItems(responseJson);
                    return;
                }

                setItems([]);
            })
            .catch((error) => {
                setError('' + error);
                setItems([]);
            });
    };

    useEffect(() => {
        searchViaAjax(props.ajaxUrl, search, sortField, sortDirection);
    }, [props.ajaxUrl, search, sortField, sortDirection]);

    const onRowClick = (itemId) => {
        props.setItemId(itemId);
        props.onClose();
    };

    const onColumnClick = (columnName) => {
        setSortField(columnName);
        setSortDirection(sortDirection === 'ASC' ? 'DESC' : 'ASC');
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
                                searchViaAjax(props.ajaxUrl, event.target.value, sortField, sortDirection);
                            }}
                        />
                        <button className="action-submit" type="button">
                            <span>Search</span>
                        </button>
                    </div>
                </div>
            </div>
            <div className="admin__field-complex admin__field-complex-attributes">
                <div className="admin__data-grid-wrap">
                    {error && <div>{error}</div>}
                    {!error && (
                        <DataTable
                            items={items}
                            fields={props.fields}
                            onRowClick={onRowClick}
                            onColumnClick={onColumnClick}
                        />
                    )}
                </div>
            </div>
        </div>
    );
};

export default SearchPanel;
