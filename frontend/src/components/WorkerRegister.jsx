import React, {useContext} from 'react';
import { LoadingContext } from '../App';
import { useNavigate } from "react-router-dom";

function WorkerRegister() {
    let navigate = useNavigate();
    const {setLoading} = useContext(LoadingContext);

    async function handleRegistration(event) {
        event.preventDefault();
        setLoading(true);
        /*   setErrorMessage(null);  */

        let body = JSON.stringify({
            worker_name: event.target.name.value,
            worker_tel: event.target.worker_tel.value,
            worker_email: event.target.worker_email.value,
            employment_type: event.target.employment_type.value,
        });
        
        try {
            let response = await fetch('http://localhost/mini-axami/public/api/registerWorker', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: body
            });

            let serverRes = await response.json();

            if (!response.ok) {
                throw new Error(serverRes.error || "Registrering av Worker misslyckades. Försök igen."); /* ['error'] */
            }
            console.log(serverRes);
            if (serverRes['success']) {
                return navigate("/workerLogin");
            } else {
                console.log(serverRes.error || "Server Error occurred...");
            }

        } catch (error) {
            console.error("Fel vid registrering av worker:", error);
            /*  setErrorMessage(error.message); */
        }finally{
            setLoading(false);
        }
    }

    function handleCancel(){
        console.log("Canceled");
        return navigate('/workerRegister');
    }

    return ( <>
        <div className="container d-flex justify-content-center align-items-center min-vh-100">
            <div className="card shadow-lg p-4" style={{ width: "400px", borderRadius: "12px" }}>
                <h3 className="text-center mb-4">Registrera Worker</h3>
                <form onSubmit={handleRegistration} onReset={handleCancel}>
                    <div className="mb-3">
                        <label className="form-label">Förnamn & Efternamn</label>
                        <input className="form-control" type="text" name="name" placeholder="Företagsnamn" required />
                    </div>

                    <div className="mb-3">
                        <label className="form-label">Email</label>
                        <input className="form-control" type="email" name="worker_email" placeholder="Arbetar Email" required />
                    </div>

                    <div className="mb-3">
                        <label className="form-label">Telefonnummer</label>
                        <input className="form-control" type="tel" name="worker_tel" placeholder="Ex: 0702457432" required />
                    </div>

                    <div className="mb-3">
                        <label className="form-label">Anställningstyp</label>
                        <select className="form-select" name="employment_type" required>
                            <option value="">Välj Anställningstyp</option>
                            <option value="Anställningstyp">Tillsvidareanställning</option>
                            <option value="Visstidsanställning">Visstidsanställning</option>
                            <option value="Provanställning">Provanställning</option>
                            <option value="Konsult ">Konsult</option>
                            <option value="Timanställning">Timanställning</option>
                        </select>
                    </div>

                    <div className="d-flex justify-content-between">
                        <button type="submit" className="btn btn-primary w-50 me-2">Registrera</button>
                        <button type="reset" className="btn btn-secondary w-50">Avbryt</button>
                    </div>
                </form>
            </div>
        </div>
    
    </> );
}

export default WorkerRegister;