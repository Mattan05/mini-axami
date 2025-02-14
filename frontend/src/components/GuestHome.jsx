import { Link } from 'react-router-dom';
function GuestHome () {
    return ( 
    <>
        <h1 className='text-center fw-bold'>Välkommen till Mini Axami</h1>
            <div className='w-100 text-center'>
                <Link className="btn btn-primary" to="/register">Registrera ditt företag </Link>
                <Link className="btn btn-danger" to="/customerLogin">Logga in customer</Link>
                <Link className="btn btn-success" to="/activation">Aktivera licens</Link>
                <hr />
                <Link className="btn btn-primary" to="/workerRegister">Registrera en worker</Link>
                <Link className="btn btn-danger" to="/workerLogin">Logga in worker</Link>
                
               
            <div/>
        </div>          
    </> 
    );
}

export default GuestHome ;