import React, { useContext, useEffect, useState } from 'react';
import { LoadingContext, AuthContext } from '../App';
import { useNavigate } from "react-router-dom";
import Select from 'react-select';
import makeAnimated from 'react-select/animated';

function WorkerLogin() {
    let navigate = useNavigate();
    const animatedComponents = makeAnimated();
    const { setLoading } = useContext(LoadingContext);
    const { userId, setUserId, userRole, setUserRole, userName, setUserName, checkSessionStatus } = useContext(AuthContext); /* behöver egentligen ej här */
    const [allCompanies, setAllCompanies] = useState([]);
    const [selectedCompany, setSelectedCompany] = useState(null); 

    useEffect(() => {
        getAllCompanies();
    }, []);

    async function getAllCompanies() {
        try {
            let response = await fetch('http://localhost/mini-axami/public/api/getAllCompanies', {
                method: 'GET',
                headers: { 'Content-Type': 'application/json' },
            });
            let serverRes = await response.json();

            if (!response.ok) {
                console.log(serverRes.error || "Hämtning av företag misslyckades. Försök igen.");
                return;
            }

            if (serverRes['success']) {
                setAllCompanies(serverRes['success']);
            } else {
                console.log(serverRes.error || "Server Error occurred...");
            }
        } catch (error) {
            console.error("Fel vid hämtning av företag:", error);
        }
    }

    async function handleLogin(event) {
        event.preventDefault();
        setLoading(true);

        if (!selectedCompany) {
            console.error("Inget företag valt.");
            setLoading(false);
            return;
        }

        let body = JSON.stringify({
            worker_email: event.target.email.value,
            company_choice: selectedCompany.value // Skickar endast valt företag, inte en array
        });

        try {
            let response = await fetch('http://localhost/mini-axami/public/api/loginWorker', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: body
            });

            let serverRes = await response.json();

            if (!response.ok) {
                throw new Error(serverRes.error || "Inloggning misslyckades. Försök igen.");
            }

            if (serverRes['success']) {
                setLoading(false);
                const loginType = 'worker';
                const workerCompanyId = serverRes['companyId'];
                return navigate("/passwordValidation", { state: { loginType, workerCompanyId } });
            } else {
                throw new Error(serverRes.error || "Server Error occurred...");
            }
        } catch (error) {
            console.error("Fel vid inloggning:", error);
            setLoading(false);
        }
    }

    function handleCancel() {
        console.log("Canceled");
        return navigate('/');
    }

    return (
        <div className="container d-flex justify-content-center align-items-center min-vh-100">
            <div className="card shadow-lg p-4" style={{ width: "400px", borderRadius: "12px" }}>
                <h3 className="text-center mb-4">Logga in Worker</h3>
                <form onSubmit={handleLogin} onReset={handleCancel}>
                    <div className="mb-3">
                        <label className="form-label">Välj företag att logga in på</label>
                        {allCompanies ? 
                            <Select
                                name="companyChoice"
                                options={allCompanies.map(company => ({ value: company.id, label: company.name }))}
                                className="basic-single"
                                classNamePrefix="Välj Företag"
                                components={animatedComponents}
                                value={selectedCompany}
                                onChange={setSelectedCompany} 
                            />
                            : <>Laddar företag...</> /* FIXA HÄR DETTA STRULAR TILL DET... FÅR INTE DET ATT SYNAS */
                            /* DET JAG FÖRSÖKER GÖRA ÄR ATT JAG MÅSTE VÄLJA FÖRETAGET WORKERN SKA LOGGA IN FÖR EFTERSOM WORKER KAN JOBBA FÖR FLERA */
                        }
                        <label className="form-label">Email</label>
                        <input className="form-control" type="email" name="email" placeholder="Din Email" required />
                    </div>
                    <div className="d-flex justify-content-between">
                        <button type="submit" className="btn btn-primary w-50 me-2">Logga in</button>
                        <button type="reset" className="btn btn-secondary w-50">Avbryt</button>
                    </div>
                </form>
            </div>
        </div>
    );
}

export default WorkerLogin;
