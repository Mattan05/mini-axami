import React, {useContext} from 'react';
import { LoadingContext, AuthContext } from '../App';
import { useNavigate } from "react-router-dom";

function CreateUnit() {
    let navigate = useNavigate();
    const {setLoading} = useContext(LoadingContext);
    const {isAuth} = useContext(AuthContext);


    async function handleCreation(event){
        event.preventDefault();
        setLoading(true);
  
          let body = JSON.stringify({
              name: event.target.unit_name.value,
              description: event.target.unit_description.value
          });

          try {
            let response = await fetch('http://localhost/mini-axami/public/api/createUnit', {
                method: 'POST',
                credentials: "include",
                headers: {
                    'Content-Type': 'application/json'
                },
                body: body 
            });

              let serverRes = await response.json();
  
              if (!response.ok) {
                  throw new Error(serverRes.error || "Skapande av Unit misslyckades. Försök igen.");
              }
              console.log(serverRes);
              if (serverRes['success']) {
                  setLoading(false);
                return navigate("/unitShow"); 
              } else {
                  throw new Error(serverRes.error || "Server Error occurred...");
              }
  
          } catch (error) {
              console.error("Fel vid skapande av maskin:", error);
              setLoading(false);
          }
    }

    async function handleCancel(event){
        event.preventDefault(); //GLÖM EJ DETTA PÅ DESSA CACNCEL!
        console.log("Canceled");
        navigate('/home')
    }
    return ( <>
        {isAuth ? 
        <div className="container d-flex justify-content-center align-items-center min-vh-100">
            <div className="card shadow-lg p-4" style={{ width: "400px", borderRadius: "12px" }}>
                <h3 className="text-center mb-4">Skapa Unit</h3>
                <form onSubmit={handleCreation} onReset={handleCancel}>
                    <div className="mb-3">
                        <label className="form-label">Unit Namn</label>
                        <input className="form-control" type="text" name="unit_name" placeholder="Namn på din Unit" required />
                    </div>

                    <div className="mb-3">
                        <label className="form-label">Beskrivning</label>
                        <textarea className="form-control" type="text" name="unit_description" placeholder="Beskrivning av Unit" required />
                    </div>

                    <div className="d-flex justify-content-between">
                        <button type="submit" className="btn btn-primary w-50 me-2">Skapa</button>
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

export default CreateUnit;