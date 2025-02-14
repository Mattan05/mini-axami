import React, {useContext} from 'react';
import { LoadingContext } from '../App';
import { useNavigate } from "react-router-dom";


function LicenseActivation() {
    const {setLoading} = useContext(LoadingContext);
    const navigate = useNavigate();

    async function handleActivation(event){
        event.preventDefault();
        setLoading(true);

        let body = JSON.stringify({
            licenseKey: event.target.licenseKey.value,
            identificationNumber: event.target.identificationNumber.value,
        });

        try {
            let response = await fetch('http://localhost/mini-axami/public/api/licenseRegistration', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: body
            });

            let serverRes = await response.json();

            if (!response.ok) {
                throw new Error(serverRes.error || "Aktivering misslyckades. Försök igen.");
            }
            console.log(serverRes);
            if (serverRes['success']) {
                setLoading(false);
                console.log(serverRes['success']);
              return navigate("/customerLogin");
            } else {
                throw new Error(serverRes.error || "Server Error occurred...");
            }

        } catch (error) {
            console.error("Fel vid Aktivering:", error);
           /*  setErrorMessage(error.message); */
            setLoading(false);
        }
    }

    function handleCancel(){
        console.log("Canceled");
        return navigate('/');
    }
    return ( <>
         <div className="container d-flex justify-content-center align-items-center min-vh-100">
            <div className="card shadow-lg p-4" style={{ width: "400px", borderRadius: "12px" }}>
                <h3 className="text-center mb-4">Aktivera Licensenyckel</h3>
                <form onSubmit={handleActivation} onReset={handleCancel}>
                    <div className="mb-3">
                        <label className="form-label">Identifikationsnummer</label>
                        <input className="form-control" type="text" name="identificationNumber" placeholder="Ex: 1234567890" required />
                    </div>
                    <label className="form-label">Licensenyckel</label>
                    <input className="form-control" type="text" name="licenseKey" placeholder="Din licensenyckel" />
                    <div className="d-flex justify-content-between">
                        <button type="submit" className="btn btn-primary w-50 me-2">Aktivera Konto</button>
                        <button type="reset" className="btn btn-secondary w-50">Avbryt</button>
                    </div>
                </form>
            </div>
        </div>
    </> );
}

export default LicenseActivation;