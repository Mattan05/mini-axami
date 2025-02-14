import React from "react";
import { useContext } from "react";
import { AuthContext } from "../App";
import { useNavigate } from "react-router-dom";

  

function Unit({unit}) {
    const {isAuth} = useContext(AuthContext);
     
    const navigate = useNavigate();
    const unitPage = () => {
        console.log(unit.unit_id);
        navigate('/unit/'+ unit.unit_id);
    }
    
    return ( <>
        <div className="card mb-4" onClick={unitPage}>
            <div className="card-header bg-dark text-white">
                <h5>{unit.name}</h5>
            </div>
            <div className="card-body">
                <p className="card-text"><strong>Beskrivning: </strong>: {unit.description}</p>

                <div className="row">
                    <div className="col-md-10">
                        <p><strong>Skapad av:</strong> <span className="text-info">{unit.customer}</span></p>
                        <p><strong>Tillagd tid:</strong> <span className="text-muted">{unit.timestamp}</span></p>
                    </div>

                    <div className="col-md-2 text-right">
                        {unit.status === 'Running' ? (
                        <p className="badge bg-success text-white py-2 px-4">Status: <strong>{unit.status}</strong></p>
                        ) : (
                        <p className="badge bg-danger text-white py-2 px-4">Status: <strong>{unit.status}</strong></p>
                        )}
                    </div>
                </div>

                {unit.notes && (
                <div className="mt-4">
                    <h6 className="text-warning"><strong>Anteckningar:</strong></h6>
                    <p>{unit.notes}</p>
                </div>
                )}
            </div>
        </div>


    {/* RETUNERA STATUS OCH NAMN IST FÖR ID FÖR SKAPAREN FRÅN SERVERN I SUCCESS */}
    
    </> );
}

export default Unit;