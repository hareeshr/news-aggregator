import React, { useState, useContext } from 'react';
import { useMutation } from 'react-query';
import { toast } from 'react-toastify';
import { z, ZodError } from 'zod';
import { AuthContext } from './../context/AuthContext';
import { CubeTransparentIcon } from '@heroicons/react/20/solid';


interface RegisterFormData {
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
      const response = await fetch('http://localhost:8000/api/register', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData),
      });
      const data = await response.json();
      // Handle response data here
      if (response.ok) {
        // Registration successful
        // toast.success(data.message);
        const response = await fetch('http://localhost:8000/api/login', {
          method: 'POST',
          body: JSON.stringify(formData),
          headers: {
            'Content-Type': 'application/json',
          },
        });
        const data = await response.json();
        // Handle response data here
        if (response.ok) {
          // Login successful
          toast.success('Logged in successfully.');
          localStorage.setItem('token', data.token);
          handleLogin();
        } else {
          // Login failed
          toast.error('Failed to login.');
        }
        setIsLoading(false);
      } else {
        // Registration failed
        toast.error(data.message);
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
          <button type="submit" className="bg-gray-700 text-white px-2 py-1" disabled={isLoading}>Register</button>
          {isLoading && <CubeTransparentIcon className="w-5 h-5 m-1.5 animate-spin" />}
        </div>
      </form>
    </div>
  );
};

export default Register;
