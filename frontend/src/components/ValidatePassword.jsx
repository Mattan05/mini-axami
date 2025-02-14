
import React, {useContext} from 'react';
import { LoadingContext, AuthContext } from '../App';
import { useNavigate, Navigate } from "react-router-dom";

function ValidatePassword() {

    let navigate = useNavigate();
    const {setLoading} = useContext(LoadingContext);
    const {setIsAuth, userId, setUserId, userRole, setUserRole, userName, setUserName, checkSessionStatus} = useContext(AuthContext);

    async function handleValidation(event) {
        event.preventDefault();
        setLoading(true);
        /*   setErrorMessage(null);  */// Nollställ tidigare felmeddelanden

        let body = JSON.stringify({
            password:event.target.password.value
        });

        try {
            let response = await fetch('http://localhost/mini-axami/public/api/passwordLess', {
                method: 'POST',
                credentials: "include",
                headers: { 'Content-Type': 'application/json' },
                body: body
            });

            let serverRes = await response.json();

            if (!response.ok) {
                throw new Error(serverRes.error || "Inloggning misslyckades. Försök igen.");
            }
            if (serverRes.success) {
                console.log('Login Successful');
                setLoading(false);
                setUserId(serverRes['success']['user_id']);
                setUserName(serverRes['success']['name']);
                setUserRole(serverRes['success']['role']);
                setIsAuth(true);
/* RETUNERA FRÅN SERVERN HELA USERN. SÅ MAN FÅR ACTIVE_USER STATE */
                    return navigate("/home");
               /*      return <Navigate to="/home"/> */
            } else {
                console.log("Something went wrong");
                setIsAuth(false);
                throw new Error(serverRes.error || "Server Error occurred...");
                
            }

        } catch (error) {
            setIsAuth(false);
            console.error("Fel vid inmatning av lösenord:", error);
            setLoading(false);
        }
    }

    function handleCancel(){
        console.log("Canceled");
        return navigate('/customerLogin');
    }
    return ( <>
        <div className="container d-flex justify-content-center align-items-center min-vh-100">
            <div className="card shadow-lg p-4" style={{ width: "400px", borderRadius: "12px" }}>
                <h3 className="text-center mb-4">Ange Engångslösenord</h3>
                <form onSubmit={handleValidation} onReset={handleCancel}>
                    <p>En engångskod har skickats till din e-post. Ange den nedan för att logga in</p>
                    <div className="mb-3">
                        <input className="form-control" type="text" name="password" placeholder="Engångslösenord" required />
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

export default ValidatePassword;