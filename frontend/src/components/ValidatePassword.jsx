
import React, {useContext, useState} from 'react';
import { LoadingContext, AuthContext } from '../App';
import { useNavigate, Navigate, useLocation } from "react-router-dom";

function ValidatePassword() {

    let navigate = useNavigate();
    let location = useLocation();
    let loginType = location.state?.loginType
    let workerCompanyId = location.state?.workerCompanyId
    const {setLoading} = useContext(LoadingContext);
    const {setIsAuth, userId, setUserId, userRole, setUserRole, userName, setUserName, checkSessionStatus} = useContext(AuthContext);

   console.log("Användartyp:", loginType);
    async function handleValidation(event) {
        event.preventDefault();
        setLoading(true);
        /*   setErrorMessage(null);  */
        console.log("workerCompanyId");
        console.log(workerCompanyId);
        /* SEN I WORKER LOGIN PÅ CLIENTEN SKA JAG SKICKA MED USELOCATION OCKSÅ DÄR DET SKA STÅ "worker" */


        let body = JSON.stringify({
            password:event.target.password.value,
            account_type: loginType,
            companyId: workerCompanyId
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
            console.log(serverRes.success);
            console.log(serverRes);
            if (serverRes.success) {
                console.log('Login Successful');
                setLoading(false);
                setUserId(serverRes['success']['user_id']);
                setUserName(serverRes['success']['name']);
                setUserRole(serverRes['success']['role']);
                setIsAuth(true);
/* RETUNERA FRÅN SERVERN HELA USERN. SÅ MAN FÅR ACTIVE_USER STATE */
                if(loginType === 'worker'){
                    return navigate('/workerHome');
                }else if(loginType === 'customer'){
                    return navigate("/home");
                }
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
        if(loginType === 'worker'){
            return navigate('/workerLogin');
        }else if(loginType === 'customer'){
            return navigate('/customerLogin');
        }
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