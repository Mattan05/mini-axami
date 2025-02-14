
import React, {useContext} from 'react';
import { LoadingContext, AuthContext } from '../App';
import { useNavigate } from "react-router-dom";

function WorkerLogin() {
    let navigate = useNavigate();
    const {setLoading} = useContext(LoadingContext);
    const {userId, setUserId, userRole, setUserRole, userName, setUserName, checkSessionStatus} = useContext(AuthContext);

    async function handleLogin(event) {
        event.preventDefault();
        setLoading(true);
        /*   setErrorMessage(null);  */// Nollställ tidigare felmeddelanden

        let body = JSON.stringify({
            worker_email:event.target.email.value
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
                return navigate("/passwordValidation", { state: { loginType } });
            } else {
                throw new Error(serverRes.error || "Server Error occurred...");
            }
        } catch (error) {
            console.error("Fel vid inloggning:", error);
            setLoading(false);
        }
    }

    function handleCancel(){
        console.log("Canceled");
        return navigate('/');
    }
    
    return ( 
    <>
        <div className="container d-flex justify-content-center align-items-center min-vh-100">
            <div className="card shadow-lg p-4" style={{ width: "400px", borderRadius: "12px" }}>
                <h3 className="text-center mb-4">Logga in Worker</h3>
                <form onSubmit={handleLogin} onReset={handleCancel}>
                    <div className="mb-3">
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
    </> );
}

export default WorkerLogin;