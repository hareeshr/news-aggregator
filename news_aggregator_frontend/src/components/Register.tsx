import React, { useState, useContext } from 'react';
import { useMutation } from 'react-query';
import { toast } from 'react-toastify';
import { z, ZodError } from 'zod';
import { CubeTransparentIcon } from '@heroicons/react/20/solid';
import { AuthContext } from './../context/AuthContext';
import { API_BASE_URL } from './../config/api';

type RegisterFormData = {
  name: string;
  email: string;
  password: string;
}

const Register = () => {
  const { handleLogin } = useContext(AuthContext);
  const [isLoading, setIsLoading] = useState(false);

  const registerMutation = useMutation(
    async (formData: RegisterFormData) => {
      setIsLoading(true);
      const registerResponse = await fetch(`${API_BASE_URL}/register`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData),
      });
      const registerData = await registerResponse.json();
      
      if (registerResponse.ok) {
        const loginResponse = await fetch(`${API_BASE_URL}/login`, {
          method: 'POST',
          body: JSON.stringify(formData),
          headers: {
            'Content-Type': 'application/json',
          },
        });
        const loginData = await loginResponse.json();
        
        if (loginResponse.ok) {
          toast.success('Logged in successfully.');
          localStorage.setItem('token', loginData.token);
          handleLogin();
        } else {
          toast.error('Failed to login.');
        }
        setIsLoading(false);
      } else {
        toast.error(registerData.message);
        setIsLoading(false);
      }
    }
  );

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    const formData: RegisterFormData = {
      name: (event.currentTarget.elements.namedItem('name') as HTMLInputElement).value,
      email: (event.currentTarget.elements.namedItem('email') as HTMLInputElement).value,
      password: (event.currentTarget.elements.namedItem('password') as HTMLInputElement).value,
    };

    try {
      registerFormSchema.parse(formData);
      registerMutation.mutate(formData);
    } catch (error: unknown) {
      if (error instanceof ZodError) {
        toast.error(error.errors[0].message);
      } else {
        toast.error('An error occurred during registration.');
      }
    }
  };

  const registerFormSchema = z.object({
    name: z.string().nonempty('Name is required'),
    email: z.string().email('Invalid email').nonempty('Email is required'),
    password: z.string().min(8, 'Password must be at least 6 characters').nonempty('Password is required'),
  });

  return (
    <div className="absolute bg-gray-300 p-2 top-20 right-5 w-[250px]">
      <h1 className="text-lg font-medium">Register</h1>
      <form onSubmit={handleSubmit}>
        <div>
          <input type="text" id="name" name="name" placeholder="Name" className="mb-2 w-full" />
        </div>
        <div>
          <input type="email" id="email" name="email" placeholder="Email" className="mb-2 w-full" />
        </div>
        <div>
          <input type="password" id="password" name="password" placeholder="Password" className="mb-2 w-full" />
        </div>
        <div className="flex">
          <button type="submit" className="bg-gray-700 text-white px-2 py-1" disabled={isLoading}>
            Register
          </button>
          {isLoading && <CubeTransparentIcon className="w-5 h-5 m-1.5 animate-spin" />}
        </div>
      </form>
    </div>
  );
};

export default Register;
