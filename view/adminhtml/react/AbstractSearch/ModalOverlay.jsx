import React, {useEffect} from "react";

const ModalOverlay = (props) => {
    useEffect(() => {
        document.body.classList.add('__has_modal');
        return () => {
            document.body.classList.remove('__has_modal');
        }
    }, []);

    return (
        <div>
            <div className="modals-overlay" style={{zIndex: 902}}>
                &nbsp;
            </div>
            <aside
                role="dialog"
                className="modal-slide _show"
                style={{zIndex: 999}}
            >
                <div className="modal-inner-wrap">
                    <header className="modal-header">
                        <h1 className="modal-header">{props.panelTitle}</h1>
                        <button className="action-close" onClick={props.onClose}>
                            <span>Close</span>
                        </button>
                        <div className="page-main-actions">
                            <div className="page-actions">
                                <div className="page-actions-buttons">
                                    <button type="button" onClick={props.onClose}>
                                        <span>Cancel</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </header>
                    <div className="modal-content">
                        { props.children }
                    </div>
                </div>
            </aside>
        </div>
    )
};

ModalOverlay.defaultProps = {
    onClose: () => alert('ModalOverlay property "onClose" is not set yet'),
    panelTitle: 'Your panel title'
};

export default ModalOverlay;
