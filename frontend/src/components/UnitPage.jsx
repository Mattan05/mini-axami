import { useContext } from "react";
import React, { useEffect, useState, useContext }  from "react";
import { useParams, useNavigate,  } from "react-router-dom";
import { LoadingContext } from "../App";

function UnitPage() {
    const {setLoading, loading} = useContext(LoadingContext); /* LOADING FASTNAR HÄR OCKSÅ?? VARFÖR */
    const { id } = useParams();
    const navigate = useNavigate();
    const [unit, setUnit] = useState(null);
    const [error, setError] = useState(null);
/*     setLoading(true); */

    useEffect(() => {
        getUnit();
    }, [id]);

        async function getUnit() {
            try {
                const res = await fetch(`http://localhost/mini-axami/public/api/unit/${id}`);
                const data = await res.json();
                console.log("getUnit();");
                if (!res.ok) {
                    console.log("Fel vid hämtning av enhet.");
                }

                if (data.success) {
                    console.log(data.success);
                    setUnit(data.success);
                }
            } catch (error) {
                setError(error.message);
            } /* finally {
                setLoading(false);
            } */
        }


    async function handleDelete() {
        setLoading(true);
        if (window.confirm("Är du säker på att du vill radera enheten?")) {
            try {
                const res = await fetch(`http://localhost/mini-axami/public/api/unit/delete/${id}`, {
                    method: "POST",
                });

                if (!res.ok) {
                    console.log("Kunde inte radera enheten.");
                }

                alert("Enheten raderades!");
                navigate("/unitShow");
            } catch (error) {
                console.log("Fel vid radering: " + error.message);
            }finally{
                setLoading(false);
            }
        }
    };

    const handleUpdate = () => {
        navigate(`/unit/update/${id}`);
    };

    const addNote= () =>{
        /* LÖS SENARE... */
    }

    
    if (error) return <p className="text-danger">{error}</p>;
    /* if (!unit) return <p className="text-muted">Ingen enhet hittades.</p>; */

    return (
        <>
        {unit ? 
        <div className="container mt-4">
            <div className="card mb-4">
                <div className="card-header bg-dark text-white">
                    <h5>{unit.name}</h5>
                </div>
                <div className="card-body">
                    <p className="card-text"><strong>Beskrivning: </strong> {unit.description}</p>

                    <div className="row">
                        <div className="col-md-10">
                            <p><strong>Tillhör:</strong> <span className="text-info">{unit.customer}</span></p>
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

                    <div className="mt-4 d-flex gap-2">
                        <button className="btn btn-primary" onClick={handleUpdate}>Uppdatera</button>
                        <button className="btn btn-danger" onClick={handleDelete}>Radera</button>
                    </div>
                </div>
            </div>
            <div className="notes-input form-group d-block" >
                <form className="form" onSubmit={addNote}>
                    <textarea className="form-control" name="notes" placeholder="Anteckna något för uniten" />
                    <div className="text-center">
                        <input className="btn btn-warning text-center" type="submit" value="Skapa anteckning" />
                    </div>
                </form>
            </div>
        </div>
        :
        <><p className="text-muted">Ingen enhet hittades.</p></>
                }
                </>
    );
}

export default UnitPage;
