import { useState, useEffect, createContext } from 'react';
import { BrowserRouter as Router, Route, Routes, Link, useNavigate, Navigate, Outlet  } from 'react-router-dom';
import './Style.css';
/* import { fetchData } from './components/Api'; */
import Registration from './components/Registration';
import LicenseActivation from './components/LicenseActivation';
import Spinner from 'react-bootstrap/Spinner';
import Header from './components/Header';
import GuestHome from './components/GuestHome';
import Home from './components/Home';
import ValidatePassword from './components/ValidatePassword';
import Login from './components/Login';
import CreateUnit from './components/CreateUnit';
import ShowUnits from './components/ShowUnits';
import UnitPage from './components/UnitPage';
import UnitUpdate from './components/UnitUpdate';
import WorkerLogin from './components/WorkerLogin';
import WorkerRegister from './components/WorkerRegister';
import WorkerHome from './components/WorkerHome';
import ShowWorkers from './components/showWorkers';

export const LoadingContext = createContext();
export const AuthContext = createContext();

    
function App() {
    const [loading, setLoading] = useState(true);
    const [isAuth, setIsAuth] = useState(null);
    const [data, setData] = useState(null);
    const [error, setError] = useState(null);
    const [userId, setUserId] = useState(null);
    const [userRole, setUserRole] = useState([]);
    const [userName, setUserName] = useState(null);

    useEffect(() => {
        async function checkSessionStatus() {
            try {
                const res = await fetch('http://localhost/mini-axami/public/api/auth', {
                    method: 'GET',
                    credentials: 'include',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                });

                const sessionData = await res.json();

                if (sessionData && sessionData.success) {
                    setIsAuth(true);
                    setUserId(sessionData.success.user_id);
                    setUserRole(sessionData.success.role);
                    setUserName(sessionData.success.name);
                    setLoading(false);
                } else {
                    setIsAuth(false);
                    setLoading(false);
                }
            } catch (error) {
                console.error('Något gick fel vid hämtning av session:', error);
                setIsAuth(false);
                setLoading(false);
            }finally {
                setLoading(false);
            }
        }

        checkSessionStatus();
    }, []);



   /* PROBLEM 2025-02-10: När jag loggar in och kommer till /home. Om jag skriver i url:en till "/" så kan jag inte gå tillbaka till "/home". Om jag kollar network för
   /api/auth retunerar den att jag är inloggad. fel på isauth någonstans. checksessionstatus eller login  */

   function PrivateRoute({ isAuth }) {
    return isAuth ? <Outlet /> : <Navigate to="/" replace />;
}

    return (
        
        <Router basename="/mini-axami/public">
            <h1>{JSON.stringify(loading)}</h1>
            <Header isAuth={isAuth}/>
            <main>
                <LoadingContext.Provider value={{loading, setLoading, error, setError}}>
                <AuthContext.Provider value={{setIsAuth, isAuth, userId, setUserId, userRole, setUserRole, userName, setUserName}}>
                    {loading ? (
                    <div className="d-flex justify-content-center align-items-center vh-100">
                            <Spinner animation="border" style={{ width: '3rem', height: '3rem' }} />
                        </div>
                    ) : /* error ? (
                        <p style={{ color: 'red' }}>Error: {error}</p>
                    ) : */ (
                       
                     /*    <GuestHome />, */



                     /* JAG BEHÖVER TÄNKA HUR JAG VILL GÖRA MED SESSIONS NÄR DET KOMMER TILL USERS OCKSÅ. HÅLLA KOLL MELLAN WORKER OCH CUSTOMER HUR VAD HMM
                        SEN ÄVEN FUNKAR INTE PASSWORD VERFICATION FÖR WORKER PÅGRUND AV VAD JAG TROR ATT SESSIONEN KRÅNGLAR PRECIS INNAN OCH I SERVER API ROUTE SÅ ANVÄNDER
                        DEN SESSIONEN FÖR ATT HÄMTA PASSWORDET. DET TROR JAG ÄR FÖR ATT PASSWORDVALIDATION INTE ÄR UNDER PRIVATE OCH OM MAN ÄR INLOGGAD SOM FÖRETAG SÅ BLIR DET 
                        KONSTIGT
                     */
                     
                        <Routes>
                            <Route path="/" element={<GuestHome  />} />
                            <Route path="/register" element={<Registration />} />
                            <Route path="/activation" element={<LicenseActivation />} />
                           {/*  <Route path="/home" element={<Home />} /> */}
                            <Route path="/customerLogin" element={<Login />} />
                            <Route path="/workerLogin" element={<WorkerLogin />} />
                            <Route path="/passwordValidation" element={<ValidatePassword />} />
                            {/* <Route path="/unit/create" element={<CreateUnit />} />
                            <Route path="/unit/show" element={<ShowUnits />} /> */}

                            {/* Skyddade routes */}
                            <Route element={<PrivateRoute isAuth={isAuth} />}>
                                <Route path="/home" element={<Home />} />
                                <Route path="/unitCreate" element={<CreateUnit />} />
                                <Route path="/unitShow" element={<ShowUnits />} />
                                <Route path="/unit/:id" element={<UnitPage />} />
                                <Route path="/unit/update/:id" element={<UnitUpdate />} />
                                <Route path="/workerHome" element={<WorkerHome />} />
                                <Route path="/workerRegister" element={<WorkerRegister />} />
                                <Route path="/showWorkers" element={<ShowWorkers />} />
                            </Route>
                        </Routes>
                            
                        /*   <button onClick="window.location.href='https://buy.stripe.com/test_bIYbJC56e8317Oo000'">
                                Checkout
                            </button> */
                    
                    )}
                </AuthContext.Provider> 
                </LoadingContext.Provider> 
            </main>
        </Router>
    );
}

export default App;
