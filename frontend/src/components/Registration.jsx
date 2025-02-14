import React, {useContext} from 'react';
import { LoadingContext } from '../App';
import { useNavigate } from "react-router-dom";

function Registration() {
    let navigate = useNavigate();
    const {setLoading} = useContext(LoadingContext);
    async function handleRegistration(event) {
        event.preventDefault();
        setLoading(true);
      /*   setErrorMessage(null);  */// Nollställ tidigare felmeddelanden

        let body = JSON.stringify({
            identificationNumber: event.target.identificationNumber.value,
            companyEmail: event.target.companyEmail.value,
            customerType: event.target.customerType.value,
            name: event.target.name.value,
        });

        try {
            let response = await fetch('http://localhost/mini-axami/public/api/companyRegistration', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: body
            });

            let serverRes = await response.json();

            if (!response.ok) {
                throw new Error(serverRes.error || "Registrering misslyckades. Försök igen."); /* ['error'] */
            }
            console.log(serverRes);
            if (serverRes['success']) {
              return navigate("/activation");
            } else {
                throw new Error(serverRes.error || "Server Error occurred...");
            }

        } catch (error) {
            console.error("Fel vid registrering:", error);
           /*  setErrorMessage(error.message); */
           
        }finally{
            setLoading(false);
        }
    }

    function handleCancel(){
        console.log("Canceled");
    }

    return (
        <div className="container d-flex justify-content-center align-items-center min-vh-100">
            <div className="card shadow-lg p-4" style={{ width: "400px", borderRadius: "12px" }}>
                <h3 className="text-center mb-4">Registrera och få tillgång</h3>
                <form onSubmit={handleRegistration} onReset={handleCancel}>
                    <div className="mb-3">
                        <label className="form-label">Namn</label>
                        <input className="form-control" type="text" name="name" placeholder="Företagsnamn" required />
                    </div>

                    <div className="mb-3">
                        <label className="form-label">Identifikationsnummer</label>
                        <input className="form-control" type="text" name="identificationNumber" placeholder="Ex: 123456-7890" required />
                    </div>

                    <div className="mb-3">
                        <label className="form-label">Email</label>
                        <input className="form-control" type="email" name="companyEmail" placeholder="Företagsmail" required />
                    </div>

                    <div className="mb-3">
                        <label className="form-label">Kundtyp</label>
                        <select className="form-select" name="customerType" required>
                            <option value="">Välj kundtyp</option>
                            <option value="company">Företag</option>
                            <option value="private">Privat Person</option>
                        </select>
                    </div>

                    <div className="d-flex justify-content-between">
                        <button type="submit" className="btn btn-primary w-50 me-2">Registrera</button>
                        <button type="reset" className="btn btn-secondary w-50">Avbryt</button>
                    </div>
                </form>
            </div>
        </div>
    );
}

export default Registration;
