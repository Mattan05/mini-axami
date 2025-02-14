import { useState, useContext, useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { LoadingContext } from "../App";

function UnitUpdate() {

    const { id } = useParams();
    const {setLoading} = useContext(LoadingContext);
    const [unit, setUnit] = useState(null);
    const navigate = useNavigate();
    useEffect(()=>{
         getUnit(id);
     }, []);

    async function handleUpdate(event){
        event.preventDefault();
        setLoading(true);
  
          let body = JSON.stringify({
              name: event.target.unit_name.value,
              description: event.target.unit_description.value,
              status: event.target.unit_status.value,
          });
 
  
          try {
              let response = await fetch('http://localhost/mini-axami/public/api/unit/update/'+id, {
                  method: 'PUT',
                  headers: { 'Content-Type': 'application/json' },
                  body: body
              });
  
              let serverRes = await response.json();
  
              if (!response.ok) {
                  setLoading(false);
                  throw new Error(serverRes.error || "Registrering misslyckades. Försök igen.");
              }
              console.log(serverRes);
              if (serverRes['success']) {
                  setLoading(false);

                return navigate("/unit/"+id);
              } else {
                  throw new Error(serverRes.error || "Server Error occurred...");
              }
  
          } catch (error) {
              console.error("Fel vid registrering:", error);
             /*  setErrorMessage(error.message); */
              setLoading(false);
          }
    }

    const handleCancel = ()=>{
        console.log("cancel");
        /* Navigate('/home') */
    }

    async function getUnit(id){
        /* setLoading(true); */
        try {
            let response = await fetch('http://localhost/mini-axami/public/api/unit/'+id, {
                method: 'GET',
                headers: { 'Content-Type': 'application/json' }
            });

            let serverRes = await response.json();

            if (!response.ok) {
              /*   setLoading(false); */
                throw new Error(serverRes.error || "Hämtning misslyckades. Försök igen."); /* ['error'] */
            }
            console.log(serverRes);
            if (serverRes['success']) {
              /*   setLoading(false); */
                setUnit(serverRes['success']);

            } else {
                throw new Error(serverRes.error || "Server Error occurred...");
            }

        } catch (error) {
            console.error("Fel vid hämtning:", error);
           /*  setErrorMessage(error.message); */
           /*  setLoading(false); */
        }
    }

    return ( <>
        {unit !== null ? 
        <div className="container d-flex justify-content-center align-items-center min-vh-100">
            <div className="card shadow-lg p-4" style={{ width: "400px", borderRadius: "12px" }}>
                <h3 className="text-center mb-4">Uppdatera Unit</h3>
                <form onSubmit={handleUpdate} onReset={handleCancel}>

                <div className="mb-3">
                        <label className="form-label">Unit Namn</label>
                        <input className="form-control" type="text" name="unit_name" placeholder={unit.name} />
                    </div>

                    <div className="mb-3">
                        <label className="form-label">Beskrivning</label>
                        <textarea className="form-control" type="text" name="unit_description" placeholder={unit.description} />
                    </div>

                    <div className="mb-3">
                        <label className="form-label">Status</label>
                        <select className="form-select" name="unit_status">
                            <option value="">Nuvarande Status: {unit.status}</option>
                            <option value="Running">Running</option>
                            <option value="Stopped">Stopped</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Error">Error</option>
                        </select>
                    </div>

                    <div className="d-flex justify-content-between">
                        <button type="submit" className="btn btn-primary w-50 me-2">Uppdatera</button>
                        <button type="reset" className="btn btn-secondary w-50">Avbryt</button>
                    </div>
                </form>
            </div>
        </div>
        :
        <></>
        }
    
    </> );
}

export default UnitUpdate;