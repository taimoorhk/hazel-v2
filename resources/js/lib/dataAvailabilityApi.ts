// Data Availability API client
export interface DataAvailabilityResponse {
  success: boolean;
  data: {
    has_data: boolean;
    has_canary_data: boolean;
    file_count: number;
    canary_file_count: number;
    last_modified: string | null;
    path: string;
    account_id: number;
    profile_id: number;
    error?: string;
  };
  message: string;
}

export interface AccountDataAvailabilityResponse {
  success: boolean;
  data: {
    account_id: number;
    main_account: {
      has_data: boolean;
      has_canary_data: boolean;
      file_count: number;
      last_modified: string | null;
    };
    elderly_profiles: Record<string, {
      has_data: boolean;
      has_canary_data: boolean;
      file_count: number;
      last_modified: string | null;
    }>;
    summary: {
      has_main_data: boolean;
      has_elderly_data: boolean;
      has_any_data: boolean;
      elderly_profiles_with_data: number[];
      total_elderly_profiles: number;
      elderly_profiles_with_data_count: number;
    };
  };
  message: string;
}

export interface DataStatusSummaryResponse {
  success: boolean;
  data: {
    account_id: number;
    main_profile: {
      has_data: boolean;
      has_canary_data: boolean;
      file_count: number;
      last_modified: string | null;
    };
    elderly_profiles: Record<string, {
      has_data: boolean;
      has_canary_data: boolean;
      file_count: number;
      last_modified: string | null;
    }>;
    overall_status: {
      has_any_data: boolean;
      has_main_data: boolean;
      has_elderly_data: boolean;
      total_elderly_profiles_with_data: number;
      total_profile_folders_found: number;
      main_account_has_folder: boolean;
    };
    digitalocean_structure: {
      all_profile_folders_found: number[];
      main_account_folder: number | null;
      elderly_profile_folders: number[];
    };
  };
  timestamp: string;
  message: string;
}

export interface ElderlyProfilesDataStatusResponse {
  success: boolean;
  data: {
    account_id: number;
    elderly_profiles_with_data: number[];
    elderly_profiles_without_data: number[];
    total_elderly_profiles_in_system: number;
    total_elderly_profiles_with_data: number;
    total_elderly_profiles_missing_data: number;
  };
  timestamp: string;
  message: string;
}

export interface ElderlyProfilesWithDataResponse {
  success: boolean;
  data: {
    account_id: number;
    elderly_profile_ids_with_data: number[];
    total_elderly_profiles_with_data: number;
  };
  timestamp: string;
  message: string;
}

export interface ComprehensiveElderlyProfilesStatusResponse {
  success: boolean;
  data: {
    account_id: number;
    elderly_profiles_status: Record<string, {
      profile_id: number;
      has_data: boolean;
      has_canary_data: boolean;
      file_count: number;
      canary_file_count: number;
      last_modified: string | null;
      status: 'has_data' | 'no_data';
    }>;
    summary: {
      total_elderly_profiles_in_system: number;
      elderly_profiles_with_data: number[];
      elderly_profiles_without_data: number[];
      total_with_data: number;
      total_without_data: number;
    };
  };
  timestamp: string;
  message: string;
}

class DataAvailabilityApi {
  private baseUrl = '/api/data-availability';

  async checkProfileData(accountId: number, profileId: number): Promise<DataAvailabilityResponse> {
    const response = await fetch(`${this.baseUrl}/check-profile`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        account_id: accountId,
        profile_id: profileId
      })
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    return response.json();
  }

  async checkMultipleProfiles(accountId: number, profileIds: number[]): Promise<{ success: boolean; data: Record<string, any> }> {
    const response = await fetch(`${this.baseUrl}/check-multiple-profiles`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        account_id: accountId,
        profile_ids: profileIds
      })
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    return response.json();
  }

  async checkAccountData(accountId: number): Promise<AccountDataAvailabilityResponse> {
    const response = await fetch(`${this.baseUrl}/check-account`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        account_id: accountId
      })
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    return response.json();
  }

  async realtimeDataCheck(accountId: number, profileId?: number): Promise<DataAvailabilityResponse> {
    const response = await fetch(`${this.baseUrl}/realtime-check`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        account_id: accountId,
        profile_id: profileId
      })
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    return response.json();
  }

  async getDataStatusSummary(accountId: number): Promise<DataStatusSummaryResponse> {
    const response = await fetch(`${this.baseUrl}/status-summary`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        account_id: accountId
      })
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    return response.json();
  }

  async getElderlyProfilesDataStatus(accountId: number, expectedElderlyProfileIds?: number[]): Promise<ElderlyProfilesDataStatusResponse> {
    const response = await fetch(`${this.baseUrl}/elderly-profiles-data-status`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        account_id: accountId,
        expected_elderly_profile_ids: expectedElderlyProfileIds || []
      })
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    return response.json();
  }

  async getElderlyProfilesWithData(accountId: number): Promise<ElderlyProfilesWithDataResponse> {
    const response = await fetch(`${this.baseUrl}/elderly-profiles-with-data`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        account_id: accountId
      })
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    return response.json();
  }

  async getComprehensiveElderlyProfilesStatus(accountId: number, systemElderlyProfileIds: number[]): Promise<ComprehensiveElderlyProfilesStatusResponse> {
    const response = await fetch(`${this.baseUrl}/comprehensive-elderly-profiles-status`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        account_id: accountId,
        system_elderly_profile_ids: systemElderlyProfileIds
      })
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    return response.json();
  }
}

export const dataAvailabilityApi = new DataAvailabilityApi();
